const userProfileId = document.querySelector('meta[name="user-profile-id"]')?.getAttribute('content');
const userLevelName = document.querySelector('meta[name="user-level-name"]')?.getAttribute('content') || 'User';

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        throw new Error('CSRF token not found. Please ensure <meta name="csrf-token"> is included in the HTML.');
    }
    return meta.getAttribute('content');
}

async function loadCertificatesData() {
    try {
        const response = await fetch('/student/api/certificates', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('API response (loadCertificatesData):', result);

        if (result.userCertificates) {
            renderCertificates(result);
            updateCertificatesStats(result);
        } else {
             renderCertificates({ userCertificates: [] });
        }
    } catch (error) {
        console.error('Error loading certificates data:', error);
        showError('Gagal memuat data sertifikat: ' + error.message);
    }
}

function renderCertificates(data) {
    const container = document.getElementById('certificatesContainer');
    const certificates = data.userCertificates || [];

    container.innerHTML = certificates.length ? '' : `
        <div class="col-span-full text-center py-8">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada sertifikat</h3>
                <p class="mt-1 text-sm text-gray-500">Selesaikan kursus untuk mendapatkan sertifikat.</p>
            </div>
        </div>
    `;

    certificates.forEach(certificate => {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 cursor-pointer';
        // Fix: Use correct download route for viewing
        card.onclick = () => window.open(`/student/api/certificate/${certificate.id}`, '_blank');
        card.innerHTML = `
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-certificate text-2xl"></i>
                    </div>
                    <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">No: ${certificate.certificate_number}</span>
                </div>
                <h3 class="text-lg font-bold mb-2 line-clamp-2">${certificate.course.title}</h3>
                <p class="text-sm opacity-90">Dikeluarkan pada ${formatDate(certificate.issued_at)}</p>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                         <!-- Fix: Handle relative urls correctly if needed. Assuming thumbnail_url is relative or full -->
                        <img src="${certificate.course.thumbnail_url ? (certificate.course.thumbnail_url.startsWith('http') ? certificate.course.thumbnail_url : '/' + certificate.course.thumbnail_url.replace(/^\//, '')) : '/images/course-placeholder.jpg'}" 
                             alt="${certificate.course.title}" 
                             class="w-8 h-8 rounded object-cover"
                             onerror="this.src='/images/course-placeholder.jpg'">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${certificate.course.instructor?.name || 'Instruktur'}</p>
                            <p class="text-xs text-gray-500">Instruktur</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <button onclick="event.stopPropagation(); downloadCertificate(${certificate.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-download mr-1"></i>Unduh
                        </button>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>Diterbitkan oleh MaxCourse</span>
                        <span>${formatDate(certificate.issued_at)}</span>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

function updateCertificatesStats(data) {
    const certificates = data.userCertificates || [];
    document.getElementById('totalCertificates').textContent = certificates.length;

    const thisMonth = certificates.filter(cert => {
        const certDate = new Date(cert.issued_at);
        const now = new Date();
        return certDate.getMonth() === now.getMonth() && certDate.getFullYear() === now.getFullYear();
    }).length;
    document.getElementById('certificatesThisMonth').textContent = thisMonth;

    const uniqueInstructors = new Set(certificates.map(cert => cert.course.instructor?.name).filter(Boolean)).size;
    document.getElementById('uniqueInstructors').textContent = uniqueInstructors;
}

function downloadCertificate(certificateId) {
    // Fix: Open the download route directly in a new tab to let browser handle PDF
    window.open(`/student/api/certificate/${certificateId}/download`, '_blank');
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
    errorDiv.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-4 text-red-700">x</button>`;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

document.addEventListener('DOMContentLoaded', () => {
    loadCertificatesData();
});
