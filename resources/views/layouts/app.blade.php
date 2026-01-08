<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admission Portal</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    
    <!-- Tailwind CSS with Dark Mode -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Theme Detection Script -->
    <script>
        // Check for saved theme preference or default to system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    </script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-300 font-sans">
    @yield('content')
    
    <!-- Theme Toggle Button -->
    <button id="theme-toggle" class="fixed bottom-6 right-6 p-3 rounded-full bg-white dark:bg-dark-800 text-gray-800 dark:text-gray-200 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-dark-700 z-50">
        <span class="material-icons-outlined text-xl">dark_mode</span>
    </button>
    
    <!-- Theme Toggle Script -->
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = themeToggle.querySelector('span');
        
        function updateThemeIcon() {
            if (document.documentElement.classList.contains('dark')) {
                themeIcon.textContent = 'light_mode';
            } else {
                themeIcon.textContent = 'dark_mode';
            }
        }
        
        updateThemeIcon();
        
        themeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.contains('dark');
            
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            
            updateThemeIcon();
        });
    </script>
    
    @stack('scripts')
</body>
</html>