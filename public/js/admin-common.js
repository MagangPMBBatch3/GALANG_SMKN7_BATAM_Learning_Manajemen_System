const userProfileId = document
    .querySelector('meta[name="user-profile-id"]')
    ?.getAttribute("content");
const userLevelName =
    document
        .querySelector('meta[name="user-level-name"]')
        ?.getAttribute("content") || "User";

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        throw new Error(
            'CSRF token not found. Please ensure <meta name="csrf-token"> is included in the HTML.'
        );
    }
    return meta.getAttribute("content");
}

function getGraphqlEndpoint() {
    const meta = document.querySelector('meta[name="graphql-endpoint"]');
    if (meta) return meta.getAttribute("content");
    // fallback to relative path (will resolve relative to current page)
    // Try to build an absolute endpoint based on the current location.
    try {
        const origin = window.location.origin;
        const pathname = window.location.pathname || "";
        const marker = "/public/index.php";
        const idx = pathname.indexOf(marker);
        if (idx !== -1) {
            const prefix = pathname.substring(0, idx); // e.g. '/maxcourse'
            const endpoint = origin + prefix + marker + "/graphql";
            console.debug("Computed GraphQL endpoint:", endpoint);
            return endpoint;
        }
        const fallback = origin + "/public/index.php/graphql";
        console.debug("Fallback GraphQL endpoint:", fallback);
        return fallback;
    } catch (e) {
        return "graphql";
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

function showError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className =
        "fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded";
    errorDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-red-700">x</button>`;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

function showSuccess(message) {
    const successDiv = document.createElement("div");
    successDiv.className =
        "fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded";
    successDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-green-700">x</button>`;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 5000);
}
