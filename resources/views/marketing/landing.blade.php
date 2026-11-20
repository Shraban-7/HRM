<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexusHRM | Complete Enterprise Human Resource & Payroll Platform</title>
    <meta name="description" content="Next-generation enterprise HRMS platform. Automated progressive payroll, real-time attendance, recruitment pipeline, 360 reviews, assets & employee self-service.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Interactive Sales Demo Switcher Bar -->
    @include('components.demo-bar')

    <!-- Sticky Navigation -->
    <header class="sticky top-0 z-40 bg-gray-900/90 backdrop-blur-md border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-users-viewfinder text-white text-lg"></i>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-white">Nexus<span class="text-indigo-400">HRM</span></span>
            </a>

            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-gray-300">
                <a href="#features" class="hover:text-white transition">Features</a>
                <a href="#modules" class="hover:text-white transition">Modules</a>
                <a href="#pricing" class="hover:text-white transition">Pricing</a>
                <a href="#security" class="hover:text-white transition">Security</a>
                <a href="#faq" class="hover:text-white transition">FAQ</a>
                <a href="{{ route('careers.index') }}" class="text-cyan-400 hover:text-cyan-300 transition flex items-center space-x-1">
                    <span>Public Careers</span>
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="{{ route('auth.login') }}" class="text-sm font-semibold text-gray-300 hover:text-white transition px-4 py-2">
                    Client Sign In
                </a>
                <a href="#demo-modal" onclick="document.getElementById('demoModal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-semibold text-sm shadow-lg shadow-indigo-500/25 transition transform hover:-translate-y-0.5">
                    Schedule Demo
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-24 pb-20 overflow-hidden bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(99,102,241,0.25),rgba(255,255,255,0))]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-bold tracking-wide uppercase mb-8">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span>Commercial Enterprise HRMS v3.0</span>
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.1] max-w-5xl mx-auto">
                The Intelligent All-In-One <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-purple-300 to-cyan-400">
                    Enterprise HR & Payroll Platform
                </span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-gray-400 max-w-3xl mx-auto leading-relaxed">
                Empower your workforce from hiring to retiring. Automate progressive tiered tax and multi-state payroll, track biometric attendance, manage 360 performance reviews, and orchestrate employee benefits with complete compliance.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <form action="{{ route('demo.switch', 'super_admin') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base shadow-xl shadow-indigo-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                        <i class="fas fa-play text-xs text-amber-300"></i>
                        <span>Launch Live Interactive Demo</span>
                    </button>
                </form>
                <a href="#modules" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-200 font-bold text-base transition flex items-center justify-center space-x-2">
                    <i class="fas fa-layer-group text-gray-400"></i>
                    <span>Explore All 12 Modules</span>
                </a>
            </div>

            <!-- Trust Bar / Key Proofs -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto pt-10 border-t border-gray-800/80">
                <div>
                    <div class="text-3xl font-extrabold text-white">99.98%</div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold mt-1">Payroll Precision</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-indigo-400">75%</div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold mt-1">HR Admin Time Saved</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-cyan-400">100%</div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold mt-1">Tax & Audit Compliant</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-purple-400">&lt; 1 sec</div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold mt-1">Automated PDF Payslips</div>
                </div>
            </div>

            <!-- Platform Dashboard Visual Preview -->
            <div class="mt-16 relative rounded-2xl p-2 bg-gradient-to-b from-indigo-500/30 via-gray-800/50 to-gray-900 border border-indigo-500/30 shadow-2xl shadow-indigo-500/10 max-w-6xl mx-auto">
                <div class="rounded-xl overflow-hidden bg-gray-950 border border-gray-800">
                    <!-- Browser Chrome Mockup Header -->
                    <div class="bg-gray-900/90 px-4 py-3 flex items-center space-x-2 border-b border-gray-800">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        <div class="mx-auto bg-gray-800 px-6 py-1 rounded-md text-xs text-gray-400 font-mono">
                            https://app.nexushrm.com/dashboard/hr
                        </div>
                    </div>
                    <!-- Mockup Preview Body -->
                    <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                        <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase font-bold text-gray-400">Active Workforce</span>
                                <i class="fas fa-user-check text-emerald-400"></i>
                            </div>
                            <div class="text-3xl font-extrabold text-white mt-2">1,248</div>
                            <div class="text-xs text-emerald-400 mt-2 flex items-center space-x-1">
                                <i class="fas fa-arrow-up"></i>
                                <span>+12 hires this month</span>
                            </div>
                        </div>
                        <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase font-bold text-gray-400">Current Payroll Run</span>
                                <i class="fas fa-money-bill-wave text-indigo-400"></i>
                            </div>
                            <div class="text-3xl font-extrabold text-white mt-2">$482,900</div>
                            <div class="text-xs text-indigo-400 mt-2 flex items-center space-x-1">
                                <i class="fas fa-check-circle"></i>
                                <span>Multi-tier progressive tax computed</span>
                            </div>
                        </div>
                        <div class="bg-gray-900 p-6 rounded-xl border border-gray-800">
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase font-bold text-gray-400">Clock-in Compliance</span>
                                <i class="fas fa-clock text-amber-400"></i>
                            </div>
                            <div class="text-3xl font-extrabold text-white mt-2">98.4%</div>
                            <div class="text-xs text-gray-400 mt-2">Real-time attendance & shifts</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Core Enterprise Modules Grid -->
    <section id="modules" class="py-24 bg-gray-950 border-t border-gray-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Comprehensive HR Suite</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Engineered for Rapid Scaling & Compliance</h2>
                <p class="text-gray-400 mt-4 text-base">Eliminate dozens of fragmented spreadsheets and third-party tools. NexusHRM unifies every touchpoint of your human capital management into one synchronized system.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Module 1: Payroll & Tax -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-indigo-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Automated Payroll & Tax</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Configurable progressive tax brackets, pre- and post-tax deductions (401k, health), batch queue workers, and 1-click DomPDF payslip distribution.
                    </p>
                </div>

                <!-- Module 2: Attendance & Shifts -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-emerald-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Attendance, Shifts & Leaves</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Web clock-in/out with automated overtime calculation, late grace periods, rotational shift rosters, and multi-tier leave approval workflows.
                    </p>
                </div>

                <!-- Module 3: ATS Recruitment -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-cyan-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">ATS Recruitment & Careers</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Public-facing career portal (`/careers`), applicant resume parsing, calendar interview scheduling, and 1-click candidate-to-employee onboarding transition.
                    </p>
                </div>

                <!-- Module 4: 360 Performance Reviews -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-purple-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">360 Performance & Goals</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Multi-rater feedback, customizable review form templates, SMART goal progress tracking, executive rating calibration, and automated performance bonuses.
                    </p>
                </div>

                <!-- Module 5: Benefits & COBRA -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-amber-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-shield-heart"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Benefits Catalog & COBRA</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Employee health, dental, and retirement plan enrollment, automated company-employee cost splitting, and automated statutory COBRA continuation tracking.
                    </p>
                </div>

                <!-- Module 6: Assets & Equipment -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-rose-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Company Asset Tracking</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Hardware inventory lifecycle: tag generation, serial number logging, employee allocation sign-offs, return condition audit, and offboarding recovery.
                    </p>
                </div>

                <!-- Module 7: Expense Management -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-blue-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Expense Claims & Receipts</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Out-of-pocket and travel claims with encrypted receipt attachment uploads, manager approval workflows, and automated payroll reimbursement sync.
                    </p>
                </div>

                <!-- Module 8: Loans & Advances -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-yellow-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/10 text-yellow-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Loans & Salary Advances</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Employee loan application, EMI repayment schedule calculators, manager sign-offs, and automatic installment deductions directly from monthly payslips.
                    </p>
                </div>

                <!-- Module 9: Internal Help Desk -->
                <div class="bg-gray-900/60 p-8 rounded-2xl border border-gray-800 hover:border-teal-500/50 transition group">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl mb-6 group-hover:scale-110 transition">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Internal HR & IT Help Desk</h3>
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">
                        Department-routed employee ticketing for HR disputes, payroll clarifications, and IT requests with priority SLAs, comments, and status workflows.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Commercial Pricing Section -->
    <section id="pricing" class="py-24 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Transparent Commercial Plans</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Predictable Pricing for Growing Teams</h2>
                <p class="text-gray-400 mt-4 text-base">Simple, straightforward licensing. Available as a managed Cloud SaaS or on-premise Enterprise Turnkey license.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Starter Plan -->
                <div class="bg-gray-950 p-8 rounded-3xl border border-gray-800 flex flex-col justify-between">
                    <div>
                        <div class="text-lg font-bold text-white">Starter</div>
                        <div class="text-xs text-gray-400 mt-1">For growing teams up to 50 employees</div>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl font-extrabold text-white">$4</span>
                            <span class="text-gray-400 text-sm ml-2">/ user / month</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-gray-300">
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Core Employee Directory & Org Chart</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Attendance Clock-in & Calendar</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Standard Leave Approvals</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Employee Self-Service (ESS)</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8">
                        <form action="{{ route('demo.switch', 'employee') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 rounded-xl bg-gray-800 hover:bg-gray-700 font-bold text-white text-sm transition">
                                Test Drive Starter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Professional Plan (Featured) -->
                <div class="bg-gradient-to-b from-indigo-950/40 via-gray-950 to-gray-950 p-8 rounded-3xl border-2 border-indigo-500 relative flex flex-col justify-between shadow-2xl shadow-indigo-500/10">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-cyan-400 text-white font-extrabold text-xs uppercase px-4 py-1 rounded-full tracking-wide">
                        Most Popular
                    </div>
                    <div>
                        <div class="text-lg font-bold text-white">Professional</div>
                        <div class="text-xs text-indigo-300 mt-1">Up to 250 employees + Payroll automation</div>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-5xl font-extrabold text-white">$8</span>
                            <span class="text-gray-400 text-sm ml-2">/ user / month</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-gray-300">
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Everything in Starter +</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Automated Progressive Payroll & Tax</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Downloadable PDF Payslips</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Recruitment ATS & Public Careers Portal</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Performance Reviews & Goal Tracking</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Expense Claims & Asset Management</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8">
                        <form action="{{ route('demo.switch', 'hr_admin') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-white text-sm shadow-lg shadow-indigo-500/30 transition">
                                Test Drive Professional
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-gray-950 p-8 rounded-3xl border border-gray-800 flex flex-col justify-between">
                    <div>
                        <div class="text-lg font-bold text-white">Enterprise</div>
                        <div class="text-xs text-gray-400 mt-1">Unlimited scale + Custom on-premise installation</div>
                        <div class="mt-6 flex items-baseline">
                            <span class="text-4xl font-extrabold text-white">Custom</span>
                            <span class="text-gray-400 text-sm ml-2">billed annually</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-gray-300">
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Everything in Professional +</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>White-label Branding & Custom Domain</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>RESTful API & Biometric Device Webhooks</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Dedicated Database & SLA 99.99%</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <i class="fas fa-check text-indigo-400 text-xs"></i>
                                <span>Full Source Code Escrow Option</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8">
                        <form action="{{ route('demo.switch', 'super_admin') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 rounded-xl bg-gray-800 hover:bg-gray-700 font-bold text-white text-sm transition">
                                Test Drive Enterprise
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security & Compliance Section -->
    <section id="security" class="py-20 bg-gray-950 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Enterprise Security</span>
            <h2 class="text-3xl font-extrabold text-white mt-2">Bank-Grade Protection for Your Sensitive HR Data</h2>
            
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <div class="p-6 rounded-2xl bg-gray-900 border border-gray-800">
                    <i class="fas fa-lock text-indigo-400 text-2xl mb-4"></i>
                    <h4 class="text-lg font-bold text-white">256-Bit Cryptography</h4>
                    <p class="text-gray-400 text-sm mt-2">All employee banking details, tax identifiers, and salary logs are encrypted at rest with AES-256 cipher suites.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-900 border border-gray-800">
                    <i class="fas fa-shield-halved text-cyan-400 text-2xl mb-4"></i>
                    <h4 class="text-lg font-bold text-white">Immutable Audit Logging</h4>
                    <p class="text-gray-400 text-sm mt-2">Every compensation update, attendance override, and access event is recorded with IP, user-agent, and before/after diffs.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-900 border border-gray-800">
                    <i class="fas fa-user-shield text-purple-400 text-2xl mb-4"></i>
                    <h4 class="text-lg font-bold text-white">Granular Dynamic RBAC</h4>
                    <p class="text-gray-400 text-sm mt-2">Role policies prevent manager privilege escalation and ensure strict departmental isolation across team dashboards.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion -->
    <section id="faq" class="py-20 bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Got Questions?</span>
                <h2 class="text-3xl font-extrabold text-white mt-2">Frequently Asked Questions</h2>
            </div>

            <div class="mt-12 space-y-4">
                <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800">
                    <h3 class="text-base font-bold text-white flex items-center justify-between">
                        <span>Can I host NexusHRM on our own cloud infrastructure?</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                    </h3>
                    <p class="text-gray-400 text-sm mt-3">Yes. NexusHRM is packaged as a standard Laravel 13 application and can be deployed on AWS, DigitalOcean, or private Linux servers with MySQL/PostgreSQL and PHP 8.3.</p>
                </div>
                <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800">
                    <h3 class="text-base font-bold text-white flex items-center justify-between">
                        <span>How does the progressive tax engine work?</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                    </h3>
                    <p class="text-gray-400 text-sm mt-3">The TaxEngine uses configurable marginal tax brackets defined in your system settings. It accurately calculates bracket ceilings, deducts standard allowances, and itemizes deductions on payslips.</p>
                </div>
                <div class="bg-gray-950 p-6 rounded-2xl border border-gray-800">
                    <h3 class="text-base font-bold text-white flex items-center justify-between">
                        <span>Can we integrate physical biometric attendance machines?</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                    </h3>
                    <p class="text-gray-400 text-sm mt-3">Yes. NexusHRM includes dedicated webhook endpoints (`/api/v1/integrations/biometric-sync`) compatible with ZKTeco ADMS devices and standard network time clocks.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-20 bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 relative">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white">Ready to Transform Your Workplace?</h2>
            <p class="text-indigo-200 mt-4 text-base sm:text-lg max-w-2xl mx-auto">
                Join high-performing companies using NexusHRM to streamline operations, reduce compliance risk, and delight employees.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <form action="{{ route('demo.switch', 'super_admin') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white text-gray-900 font-extrabold text-base shadow-xl hover:bg-gray-100 transition">
                        Experience Full Platform Demo
                    </button>
                </form>
                <a href="{{ route('auth.login') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-indigo-950/80 hover:bg-indigo-950 text-white font-bold text-base border border-indigo-400/30 transition">
                    Client Login
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 py-12 text-gray-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white">
                    <i class="fas fa-users-viewfinder text-sm"></i>
                </div>
                <span class="text-lg font-bold text-white">NexusHRM</span>
            </div>
            <div class="text-center md:text-left text-xs text-gray-500">
                &copy; {{ date('Y') }} NexusHRM Enterprise Technologies. All rights reserved. Commercial Edition.
            </div>
            <div class="flex space-x-6 text-gray-400">
                <a href="{{ route('careers.index') }}" class="hover:text-white transition">Careers Portal</a>
                <a href="{{ route('auth.login') }}" class="hover:text-white transition">Sign In</a>
                <a href="#privacy" class="hover:text-white transition">Privacy Policy</a>
            </div>
        </div>
    </footer>

    <!-- Demo Request Modal -->
    <div id="demoModal" class="fixed inset-0 z-50 bg-gray-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 max-w-lg w-full relative shadow-2xl">
            <button onclick="document.getElementById('demoModal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
            <h3 class="text-2xl font-bold text-white">Schedule an Enterprise Demo</h3>
            <p class="text-gray-400 text-sm mt-2">Enter your corporate details and our solutions architect will guide you through a customized walkthrough.</p>
            <form onsubmit="event.preventDefault(); alert('Thank you! Our enterprise sales team will contact you within 2 business hours.'); document.getElementById('demoModal').classList.add('hidden');" class="mt-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-300 mb-1">Full Name</label>
                    <input type="text" required placeholder="Alex Morgan" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-300 mb-1">Corporate Work Email</label>
                    <input type="email" required placeholder="alex@company.com" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-300 mb-1">Employee Count</label>
                    <select class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 focus:outline-none">
                        <option>10 - 50 Employees</option>
                        <option>51 - 250 Employees</option>
                        <option>251 - 1,000 Employees</option>
                        <option>1,000+ Employees</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition">
                    Request Product Walkthrough
                </button>
            </form>
        </div>
    </div>

</body>
</html>
