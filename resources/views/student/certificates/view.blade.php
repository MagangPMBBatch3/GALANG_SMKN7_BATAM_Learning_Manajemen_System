@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('student.dashboard') }}" class="flex items-center text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>

        <div class="bg-white text-slate-900 shadow-2xl rounded-xl overflow-hidden relative border-8 border-double border-slate-200 p-8 sm:p-12 print:shadow-none print:border-none print:w-full print:h-screen print:flex print:flex-col print:justify-center">
            
            <!-- Certificate Border Decoration -->
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none p-4">
                <div class="w-full h-full border-2 border-slate-900/10 rounded-lg"></div>
            </div>

            <div class="text-center relative z-10">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto shadow-lg mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-serif font-bold text-slate-900 tracking-wider">CERTIFICATE</h1>
                    <p class="text-lg text-slate-500 uppercase tracking-[0.2em] mt-2">of Completion</p>
                </div>

                <div class="my-12">
                    <p class="text-slate-600 italic mb-4">This certificate is proudly presented to</p>
                    <h2 class="text-3xl sm:text-4xl font-bold text-blue-900 mb-2 font-serif border-b-2 border-slate-100 pb-4 inline-block px-12">{{ $certificate->user->name }}</h2>
                    
                    <p class="text-slate-600 italic mt-8 mb-4">For successfully completing the course</p>
                    <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-6">{{ $certificate->course->title }}</h3>
                    
                    <p class="text-slate-500">
                        Date Issued: <span class="font-semibold text-slate-700">{{ $certificate->issued_at->format('j F Y') }}</span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-12 mt-16 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-2">
                            <span class="font-script text-2xl text-blue-800">MaxCourse System</span>
                        </div>
                        <div class="h-px bg-slate-300 w-full mb-2"></div>
                        <p class="text-xs uppercase tracking-wider text-slate-500">Platform Verification</p>
                    </div>
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-2">
                             <!-- Generate QR Code or Signature here if needed -->
                             <span class="font-mono text-xs text-slate-400 block break-all">{{ $certificate->digital_signature }}</span>
                        </div>
                        <div class="h-px bg-slate-300 w-full mb-2"></div>
                        <p class="text-xs uppercase tracking-wider text-slate-500">Digital Signature</p>
                    </div>
                </div>

                <div class="mt-12 text-xs text-slate-400">
                    Certificate ID: {{ $certificate->cert_number }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body * {
            visibility: hidden;
        }
        .max-w-4xl, .max-w-4xl * {
            visibility: visible;
        }
        .max-w-4xl {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: none !important;
            border: none !important;
            transform: scale(1);
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="min-h-screen bg-slate-100 dark:bg-slate-900 py-12 flex items-center justify-center">
    <div class="w-full max-w-4xl px-4">
        <!-- Control Bar -->
        <div class="mb-8 flex items-center justify-between no-print">
            <a href="{{ route('student.dashboard') }}" class="flex items-center text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition group">
                <div class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center mr-3 group-hover:scale-110 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </div>
                <span class="font-medium">Kembali ke Dashboard</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-medium transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Unduh PDF
            </button>
        </div>

        <!-- Certificate Container -->
        <div class="bg-white text-slate-900 shadow-2xl rounded-none relative overflow-hidden aspect-[1.414/1] w-full mx-auto print:shadow-none">
            <!-- Decorative Border -->
            <div class="absolute inset-4 border-[12px] border-double border-blue-900/10 pointer-events-none z-20"></div>
            
            <!-- Corner Ornaments (CSS only) -->
            <div class="absolute top-4 left-4 w-24 h-24 border-t-4 border-l-4 border-blue-800 z-20"></div>
            <div class="absolute top-4 right-4 w-24 h-24 border-t-4 border-r-4 border-blue-800 z-20"></div>
            <div class="absolute bottom-4 left-4 w-24 h-24 border-b-4 border-l-4 border-blue-800 z-20"></div>
            <div class="absolute bottom-4 right-4 w-24 h-24 border-b-4 border-r-4 border-blue-800 z-20"></div>

            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-[0.03] z-0" style="background-image: radial-gradient(#1e3a8a 1px, transparent 1px); background-size: 24px 24px;"></div>
            
            <!-- Watermark -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.04] pointer-events-none z-0 transform -rotate-12">
                <svg class="w-2/3 h-2/3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
            </div>

            <!-- Content -->
            <div class="relative z-10 h-full flex flex-col items-center justify-center p-16 text-center">
                
                <!-- Header -->
                <div class="mb-10">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-graduation-cap text-lg"></i>
                        </div>
                        <span class="text-blue-900 font-bold tracking-widest uppercase text-sm">MaxCourse Learning Platform</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-serif font-bold text-blue-900 tracking-tight mb-2">CERTIFICATE</h1>
                    <p class="text-xl text-blue-800/60 font-light uppercase tracking-[0.3em]">Of Achievement</p>
                </div>

                <!-- Recipient -->
                <div class="mb-8 w-full">
                    <p class="text-slate-500 italic font-serif text-lg mb-6">This certifies that</p>
                    <div class="relative inline-block px-12 pb-2">
                        <h2 class="text-4xl md:text-5xl font-bold text-slate-900 font-serif border-b-2 border-slate-300 pb-4 min-w-[300px]">
                            {{ $certificate->user->name }}
                        </h2>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="mb-12 max-w-2xl mx-auto">
                    <p class="text-slate-500 italic font-serif text-lg mb-4">has successfully completed the course</p>
                    <h3 class="text-2xl md:text-3xl font-bold text-blue-800 mb-6 leading-tight">
                        {{ $certificate->course->title }}
                    </h3>
                    <p class="text-slate-600">
                        Date of Issue: <span class="font-semibold">{{ $certificate->issued_at->format('F j, Y') }}</span>
                    </p>
                </div>

                <!-- Signatures -->
                <div class="w-full max-w-3xl mx-auto flex justify-between items-end mt-4 px-12">
                    <div class="text-center">
                        <div class="w-48 border-b border-slate-400 pb-2 mb-2">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Signature_sample.svg/1200px-Signature_sample.svg.png" 
                                 class="h-12 mx-auto opacity-70 object-contain" alt="Signature">
                        </div>
                        <p class="font-bold text-slate-800 text-sm">MaxCourse Director</p>
                        <p class="text-xs text-slate-500">Director of Education</p>
                    </div>

                    <!-- Seal -->
                    <div class="relative group">
                        <div class="w-24 h-24 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg border-4 border-yellow-600 text-white">
                            <div class="w-20 h-20 border-2 border-dashed border-yellow-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-medal text-3xl drop-shadow-sm"></i>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 w-32 h-8 bg-blue-800 rounded-lg shadow-md flex items-center justify-center z-[-1]"></div>
                        <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 translate-y-1 w-0 h-0 border-l-[10px] border-l-transparent border-t-[10px] border-t-blue-900 border-r-[10px] border-r-transparent"></div>
                    </div>

                    <div class="text-center">
                         <div class="w-48 border-b border-slate-400 pb-2 mb-2 h-[57px] flex items-end justify-center">
                            <span class="font-mono text-[9px] text-slate-400 uppercase tracking-widest break-all line-clamp-2 leading-tight opacity-70">
                                {{ substr($certificate->digital_signature, 0, 20) }}...
                            </span>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">Digital Verification</p>
                        <p class="text-xs text-slate-500">ID: {{ $certificate->cert_number }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
