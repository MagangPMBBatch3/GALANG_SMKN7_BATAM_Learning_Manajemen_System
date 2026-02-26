// Enhanced PDF Viewer Component for Alpine.js
// Using PDF.js with better error handling and fallbacks

// PDF Manager - completely outside Alpine's reactive system
class PDFManager {
    constructor() {
        this.pdfDoc = null;
        this.totalPages = 0;
    }

    async loadPDF(arrayBuffer) {
        if (typeof pdfjsLib === "undefined") {
            throw new Error("PDF.js library not loaded");
        }

        const loadingTask = pdfjsLib.getDocument({
            data: arrayBuffer,
            cMapUrl: "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/",
            cMapPacked: true,
            isEvalSupported: false,
        });

        this.pdfDoc = await loadingTask.promise;
        this.totalPages = this.pdfDoc.numPages;
        return this.totalPages;
    }

    async getPage(num) {
        if (!this.pdfDoc) {
            throw new Error("PDF not loaded");
        }
        return await this.pdfDoc.getPage(num);
    }

    destroy() {
        if (this.pdfDoc) {
            this.pdfDoc.destroy();
            this.pdfDoc = null;
        }
    }
}

function pdfViewerEnhanced() {
    return {
        pdfUrl: "",
        currentPage: 1,
        totalPages: 0,
        scale: 1.0,
        loading: true,
        error: false,
        errorMessage: "",
        pageRendering: false,
        pageNumPending: null,
        canvasWidth: 800,
        canvasHeight: 600,
        showToolbar: true,
        isFullscreen: false,
        annotationMode: false,

        async init() {
            // Set PDF.js worker from CDN
            if (typeof pdfjsLib !== "undefined") {
                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
            }

            // Create PDF manager outside Alpine's reactive system
            this.pdfManager = new PDFManager();

            // Watch for URL changes
            this.$watch("pdfUrl", (value) => {
                if (value) {
                    this.currentPage = 1;
                    this.loadPDF();
                }
            });

            // Load PDF if URL exists
            if (this.pdfUrl) {
                await this.loadPDF();
            }

            // Setup keyboard shortcuts
            this.setupKeyboardShortcuts();
        },

        setupKeyboardShortcuts() {
            document.addEventListener("keydown", (e) => {
                if (!this.pdfManager || !this.pdfManager.pdfDoc) return;

                if (e.key === "ArrowRight" || e.key === "ArrowDown") {
                    e.preventDefault();
                    this.nextPage();
                }
                if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
                    e.preventDefault();
                    this.previousPage();
                }
                if (e.key === "+" || e.key === "=") {
                    e.preventDefault();
                    this.zoomIn();
                }
                if (e.key === "-") {
                    e.preventDefault();
                    this.zoomOut();
                }
                if (e.key === "0") {
                    e.preventDefault();
                    this.resetZoom();
                }
            });
        },

        async loadPDF() {
            this.loading = true;
            this.error = false;
            this.errorMessage = "";

            try {
                // Add cache-busting parameter
                const url =
                    this.pdfUrl +
                    (this.pdfUrl.includes("?") ? "&" : "?") +
                    "t=" +
                    Date.now();

                console.log("[PDF Viewer] Loading PDF from:", url);

                // Try to fetch with multiple strategies
                let arrayBuffer = null;
                let contentType = null;

                try {
                    // Get CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    // Strategy 1: Direct fetch with CORS and CSRF token
                    const headers = {
                        Accept: "application/pdf, */*",
                        "X-Requested-With": "XMLHttpRequest",
                    };
                    
                    // Add CSRF token if available
                    if (csrfToken) {
                        headers["X-CSRF-TOKEN"] = csrfToken;
                    }
                    
                    const response = await fetch(url, {
                        method: "GET",
                        headers: headers,
                        credentials: "same-origin",
                        mode: "same-origin",
                    });

                    console.log(
                        "[PDF Viewer] Response status:",
                        response.status
                    );
                    console.log(
                        "[PDF Viewer] Response headers:",
                        Object.fromEntries(response.headers.entries())
                    );

                    if (!response.ok) {
                        throw new Error(
                            `HTTP ${response.status}: ${response.statusText}`
                        );
                    }

                    contentType = response.headers.get("content-type");
                    arrayBuffer = await response.arrayBuffer();
                } catch (fetchError) {
                    console.warn(
                        "[PDF Viewer] Fetch strategy failed:",
                        fetchError.message
                    );

                    // Strategy 2: Try with blob
                    const response = await fetch(url, {
                        credentials: "same-origin",
                    });

                    if (!response.ok) {
                        throw fetchError;
                    }

                    const blob = await response.blob();
                    arrayBuffer = await blob.arrayBuffer();
                    contentType = blob.type;
                }

                // Validate PDF
                if (!arrayBuffer || arrayBuffer.byteLength === 0) {
                    throw new Error(
                        "PDF file is empty or could not be retrieved"
                    );
                }

                // Check if it looks like a PDF
                const view = new Uint8Array(arrayBuffer);
                if (view[0] !== 0x25 || view[1] !== 0x50 || view[2] !== 0x44) {
                    // %PD
                    const textDecoder = new TextDecoder();
                    const snippet = textDecoder.decode(view.slice(0, 200));
                    console.error(
                        "[PDF Viewer] Invalid PDF header. Content:",
                        snippet
                    );
                    throw new Error("File does not appear to be a valid PDF");
                }

                console.log(
                    "[PDF Viewer] PDF size:",
                    arrayBuffer.byteLength,
                    "bytes"
                );
                console.log("[PDF Viewer] Content type:", contentType);

                // Load with PDF.js using our isolated manager
                this.totalPages = await this.pdfManager.loadPDF(arrayBuffer);

                console.log(
                    "[PDF Viewer] PDF loaded successfully. Pages:",
                    this.totalPages
                );

                this.loading = false;
                await this.renderPage(1);
            } catch (err) {
                console.error("[PDF Viewer] Error loading PDF:", err);
                console.error("[PDF Viewer] Error details:", {
                    message: err.message,
                    name: err.name,
                    stack: err.stack,
                });

                this.error = true;
                this.loading = false;
                this.errorMessage = this.formatErrorMessage(err.message);
            }
        },

        formatErrorMessage(message) {
            if (message.includes("HTTP 204")) {
                return "File not found or is empty on the server. Please contact your instructor.";
            }
            if (message.includes("HTTP 404")) {
                return "PDF file not found on the server.";
            }
            if (message.includes("HTTP 403")) {
                return "You don't have permission to access this PDF.";
            }
            if (message.includes("does not appear to be a valid PDF")) {
                return "The file returned is not a valid PDF. The server may have returned an error page instead.";
            }
            if (message.includes("PDF.js library")) {
                return "PDF viewer library failed to load. Please refresh the page.";
            }
            return message || "Failed to load PDF";
        },

        async renderPage(num) {
            if (!this.pdfManager || !this.pdfManager.pdfDoc || num < 1 || num > this.totalPages) {
                console.warn("[PDF Viewer] Invalid render request", {
                    hasPdfDoc: !!(this.pdfManager && this.pdfManager.pdfDoc),
                    num,
                    totalPages: this.totalPages
                });
                return;
            }

            this.pageRendering = true;

            try {
                // Get the page from our isolated PDF manager
                const page = await this.pdfManager.getPage(num);
                
                const canvas = this.$refs.pdfCanvas;

                if (!canvas) {
                    console.error("[PDF Viewer] Canvas reference not found");
                    this.pageRendering = false;
                    return;
                }

                const ctx = canvas.getContext("2d");
                const viewport = page.getViewport({ scale: this.scale });

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                };

                await page.render(renderContext).promise;
                this.currentPage = num;
                this.pageRendering = false;

                console.log("[PDF Viewer] Page rendered successfully:", num);

                if (this.pageNumPending !== null) {
                    const pending = this.pageNumPending;
                    this.pageNumPending = null;
                    await this.renderPage(pending);
                }
            } catch (err) {
                console.error("[PDF Viewer] Error rendering page:", err);
                this.pageRendering = false;
                
                // If it's a critical error, show error state
                if (err.message && err.message.includes("private member")) {
                    this.error = true;
                    this.errorMessage = "PDF rendering error. Please refresh the page.";
                }
            }
        },

        queuePage(num) {
            if (num !== this.currentPage) {
                this.pageNumPending = num;
            }
            if (!this.pageRendering) {
                this.renderPage(num);
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.queuePage(this.currentPage + 1);
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.queuePage(this.currentPage - 1);
            }
        },

        goToPage(pageNum) {
            const page = parseInt(pageNum);
            if (page >= 1 && page <= this.totalPages) {
                this.queuePage(page);
            }
        },

        zoomIn() {
            if (this.scale < 3) {
                this.scale += 0.25;
                this.renderPage(this.currentPage);
            }
        },

        zoomOut() {
            if (this.scale > 0.5) {
                this.scale -= 0.25;
                this.renderPage(this.currentPage);
            }
        },

        resetZoom() {
            this.scale = 1.0;
            this.renderPage(this.currentPage);
        },

        fitToWidth() {
            if (this.$refs.pdfCanvas) {
                const canvas = this.$refs.pdfCanvas;
                const containerWidth = canvas.parentElement.clientWidth - 20;
                this.scale = containerWidth / (canvas.width / this.scale);
                this.renderPage(this.currentPage);
            }
        },

        fitToPage() {
            if (this.$refs.pdfCanvas) {
                const canvas = this.$refs.pdfCanvas;
                const container = canvas.parentElement;
                const scaleX = (container.clientWidth - 20) / canvas.width;
                const scaleY = (container.clientHeight - 20) / canvas.height;
                this.scale = Math.min(scaleX, scaleY);
                this.renderPage(this.currentPage);
            }
        },

        downloadPDF() {
            const link = document.createElement("a");
            link.href = this.pdfUrl;
            link.download = "document.pdf";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        toggleFullscreen() {
            const element = this.$el;
            if (!document.fullscreenElement) {
                element.requestFullscreen().catch((err) => {
                    console.warn(
                        "[PDF Viewer] Fullscreen request denied:",
                        err
                    );
                });
            } else {
                document.exitFullscreen();
            }
        },

        printPDF() {
            if (this.pdfUrl) {
                window.open(this.pdfUrl, "_blank");
            }
        },
    };
}
