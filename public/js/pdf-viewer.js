// PDF.js Viewer Component for Alpine.js
function pdfViewer() {
    return {
        pdfUrl: "",
        currentPage: 1,
        totalPages: 0,
        scale: 1.0,
        loading: true,
        error: false,
        errorMessage: "",
        pdfDoc: null,
        pageRendering: false,
        pageNumPending: null,

        async init() {
            // Watch for URL changes
            this.$watch("pdfUrl", (value) => {
                if (value) this.loadPDF();
            });

            // Set PDF.js worker
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

            // Load PDF
            if (this.pdfUrl) {
                await this.loadPDF();
            }
        },

        async loadPDF() {
            this.loading = true;
            this.error = false;

            try {
                // Add cache-busting parameter to force fresh request
                const url =
                    this.pdfUrl +
                    (this.pdfUrl.includes("?") ? "&" : "?") +
                    "t=" +
                    Date.now();
                console.log("[PDF Viewer] Loading PDF from:", url);

                // Fetch PDF as ArrayBuffer for better control and error handling
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        Accept: "application/pdf",
                        "X-Requested-With": "XMLHttpRequest", // Force JSON response on auth error
                    },
                    credentials: "same-origin", // Include cookies for authentication
                });

                console.log("[PDF Viewer] Response status:", response.status);
                console.log(
                    "[PDF Viewer] Response headers:",
                    Object.fromEntries(response.headers.entries())
                );

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error(
                        "[PDF Viewer] HTTP Error:",
                        response.status,
                        errorText
                    );
                    throw new Error(
                        `HTTP ${response.status}: ${response.statusText}`
                    );
                }

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/pdf")) {
                    const text = await response.text();
                    console.error(
                        "[PDF Viewer] Invalid content type:",
                        contentType
                    );
                    console.error(
                        "[PDF Viewer] Response body snippet:",
                        text.substring(0, 500)
                    );
                    throw new Error(
                        `Invalid content type: ${contentType}. Expected application/pdf`
                    );
                }

                // Get PDF as ArrayBuffer
                const arrayBuffer = await response.arrayBuffer();
                console.log(
                    "[PDF Viewer] PDF size:",
                    arrayBuffer.byteLength,
                    "bytes"
                );

                if (arrayBuffer.byteLength === 0) {
                    throw new Error("PDF file is empty");
                }

                // Load PDF with PDF.js
                const loadingTask = pdfjsLib.getDocument({
                    data: arrayBuffer, // Use ArrayBuffer instead of URL
                    cMapUrl:
                        "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/",
                    cMapPacked: true,
                });

                this.pdfDoc = await loadingTask.promise;
                this.totalPages = this.pdfDoc.numPages;
                console.log(
                    "[PDF Viewer] PDF loaded successfully. Total pages:",
                    this.totalPages
                );

                this.loading = false;

                // Render first page
                await this.renderPage(1);
            } catch (err) {
                console.error("[PDF Viewer] Error loading PDF:", err);
                console.error("[PDF Viewer] Error stack:", err.stack);
                this.error = true;
                this.loading = false;
                this.errorMessage = err.message || "Unknown error occurred";
            }
        },

        async renderPage(num) {
            this.pageRendering = true;

            try {
                const page = await this.pdfDoc.getPage(num);
                const canvas = this.$refs.pdfCanvas;
                const ctx = canvas.getContext("2d");

                const viewport = page.getViewport({ scale: this.scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                };

                await page.render(renderContext).promise;
                this.pageRendering = false;

                if (this.pageNumPending !== null) {
                    await this.renderPage(this.pageNumPending);
                    this.pageNumPending = null;
                }
            } catch (err) {
                console.error("Error rendering page:", err);
                this.pageRendering = false;
            }
        },

        async queueRenderPage(num) {
            if (this.pageRendering) {
                this.pageNumPending = num;
            } else {
                await this.renderPage(num);
            }
        },

        async previousPage() {
            if (this.currentPage <= 1) return;
            this.currentPage--;
            await this.queueRenderPage(this.currentPage);
        },

        async nextPage() {
            if (this.currentPage >= this.totalPages) return;
            this.currentPage++;
            await this.queueRenderPage(this.currentPage);
        },

        async zoomIn() {
            this.scale += 0.25;
            await this.queueRenderPage(this.currentPage);
        },

        async zoomOut() {
            if (this.scale <= 0.5) return;
            this.scale -= 0.25;
            await this.queueRenderPage(this.currentPage);
        },

        async resetZoom() {
            this.scale = 1.0;
            await this.queueRenderPage(this.currentPage);
        },
    };
}
