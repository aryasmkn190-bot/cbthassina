<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Candy Quiz - Quiz Seru Berhadiah Manis!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka+One:wght@400&family=Poppins:wght@300;400;500;600;700&display=swap');

        .candy-font {
            font-family: 'Fredoka One', cursive;
        }

        .subtle-hover {
            transition: all 0.3s ease;
        }

        .subtle-hover:hover {
            transform: translateY(-2px);
        }

        .clean-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float-bubble 6s infinite ease-in-out;
        }

        @keyframes float-bubble {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.7;
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 1;
            }
        }

        .text-bounce {
            animation: bounce-text 2s ease-in-out infinite;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .text-bounce:hover {
            transform: scale(1.1);
        }

        @keyframes bounce-text {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-8px);
            }

            60% {
                transform: translateY(-4px);
            }
        }

        /* Mobile scroll fix */
        html,
        body {
            height: 100%;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            body {
                min-height: 100vh;
                height: auto;
            }
        }
    </style>
</head>

<body class="min-h-screen clean-gradient font-['Poppins'] flex flex-col">
    <!-- Header -->
    <header class="p-6">
        <nav class="flex justify-between items-center max-w-6xl mx-auto">
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-white candy-font">Candy Quiz</h1>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="#" onclick="showHomePage()" class="text-white hover:text-yellow-200 transition-colors font-medium">Beranda</a>
                <a href="#" onclick="showQuizPage()" class="text-white hover:text-yellow-200 transition-colors font-medium">Quiz</a>
                <a href="#" onclick="showLeaderboard()" class="text-white hover:text-yellow-200 transition-colors font-medium">Leaderboard</a>
                <a href="#" class="text-white hover:text-yellow-200 transition-colors font-medium">Tentang</a>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="showLogin()" class="hidden md:block bg-white/20 text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-colors font-medium">
                    Masuk
                </button>
                <button onclick="showRegister()" class="hidden md:block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors font-medium">
                    Daftar
                </button>
                <button onclick="toggleMobileMenu()" class="md:hidden text-white text-2xl">☰</button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden bg-black/30 backdrop-blur-sm hidden">
            <div class="px-6 py-4 space-y-3">
                <a href="#" onclick="showHomePage(); closeMobileMenu();" class="block text-white hover:text-yellow-200 transition-colors font-medium py-2">Beranda</a>
                <a href="#" onclick="showQuizPage(); closeMobileMenu();" class="block text-white hover:text-yellow-200 transition-colors font-medium py-2">Quiz</a>
                <a href="#" onclick="showLeaderboard(); closeMobileMenu();" class="block text-white hover:text-yellow-200 transition-colors font-medium py-2">Leaderboard</a>
                <a href="#" class="block text-white hover:text-yellow-200 transition-colors font-medium py-2">Tentang</a>
                <div class="pt-3 border-t border-white/20 space-y-2">
                    <button onclick="showLogin(); closeMobileMenu();" class="block w-full text-left bg-white/20 text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-colors font-medium">
                        Masuk
                    </button>
                    <button onclick="showRegister(); closeMobileMenu();" class="block w-full text-left bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors font-medium">
                        Daftar
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Animated Bubbles -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="bubble w-20 h-20 top-10 left-10" style="animation-delay: 0s;"></div>
        <div class="bubble w-16 h-16 top-32 right-20" style="animation-delay: 1s;"></div>
        <div class="bubble w-12 h-12 top-64 left-32" style="animation-delay: 2s;"></div>
        <div class="bubble w-24 h-24 bottom-32 right-16" style="animation-delay: 3s;"></div>
        <div class="bubble w-14 h-14 bottom-20 left-20" style="animation-delay: 4s;"></div>
        <div class="bubble w-18 h-18 top-48 right-48" style="animation-delay: 2.5s;"></div>
        <div class="bubble w-10 h-10 bottom-48 left-48" style="animation-delay: 1.5s;"></div>
    </div>

    <!-- Home Page -->
    <main id="homePage" class="flex-1 flex items-center justify-center px-6 py-12 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="mb-12">
                <h1 class="text-5xl md:text-7xl font-bold text-white candy-font mb-6 drop-shadow-lg text-bounce">
                    Candy Quiz
                </h1>
                <div class="text-xl text-blue-100 mb-8">
                    Platform Quiz Interaktif Terbaik
                </div>
            </div>

            <p class="text-xl text-white mb-12 max-w-2xl mx-auto leading-relaxed">
                Uji pengetahuanmu dengan quiz interaktif yang menyenangkan!
                Kumpulkan poin, naik level, dan raih hadiah manis di setiap tantangan.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                <button onclick="showQuizPage()" class="bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 subtle-hover transition-all duration-300 shadow-lg min-w-[160px]">
                    Mulai Quiz
                </button>

                <button onclick="joinRoom()" class="bg-blue-500 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-600 subtle-hover transition-all duration-300 shadow-lg min-w-[160px]">
                    Gabung Room
                </button>

                <button onclick="createRoom()" class="bg-green-500 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-600 subtle-hover transition-all duration-300 shadow-lg min-w-[160px]">
                    Buat Room
                </button>
            </div>

            <!-- Features -->
            <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6 text-white hover:bg-white/20 subtle-hover transition-all duration-300">
                    <h3 class="text-lg font-semibold mb-3">Quiz Interaktif</h3>
                    <p class="text-sm opacity-90">Berbagai kategori quiz menarik dengan tingkat kesulitan yang beragam</p>
                </div>

                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6 text-white hover:bg-white/20 subtle-hover transition-all duration-300">
                    <h3 class="text-lg font-semibold mb-3">Sistem Poin</h3>
                    <p class="text-sm opacity-90">Kumpulkan poin dan bersaing dengan pemain lain di leaderboard</p>
                </div>

                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6 text-white hover:bg-white/20 subtle-hover transition-all duration-300">
                    <h3 class="text-lg font-semibold mb-3">Multiplayer</h3>
                    <p class="text-sm opacity-90">Bermain bersama teman dalam room quiz yang seru dan kompetitif</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Quiz Page -->
    <main id="quizPage" class="flex-1 px-6 py-12 relative z-10 hidden">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-white candy-font mb-4 drop-shadow-lg">
                    Pilih Quiz Favoritmu
                </h1>
                <p class="text-lg text-white/90 max-w-2xl mx-auto">
                    Berbagai kategori quiz menarik menanti! Pilih topik yang kamu sukai dan mulai petualangan belajar yang seru.
                </p>
            </div>

            <!-- Quiz Categories - Vertical List -->
            <div class="space-y-4 mb-12">
                <!-- Pengetahuan Umum -->
                <div class="bg-white rounded-2xl p-4 md:p-6 hover:shadow-xl subtle-hover transition-all duration-300 cursor-pointer border border-gray-100" onclick="selectQuiz('umum')">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-50 rounded-xl flex items-center justify-center text-2xl md:text-3xl flex-shrink-0">🧠</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">Pengetahuan Umum</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">Uji wawasan umummu dengan berbagai topik menarik</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <span>📊</span>
                                        <span>25 Soal</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>⏱️</span>
                                        <span>30 menit</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>🏆</span>
                                        <span>100 poin</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:flex-col md:items-end md:text-right space-x-4 md:space-x-0 md:space-y-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-yellow-400 text-sm">⭐⭐⭐</span>
                            </div>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                Mulai Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sejarah -->
                <div class="bg-white rounded-2xl p-4 md:p-6 hover:shadow-xl subtle-hover transition-all duration-300 cursor-pointer border border-gray-100" onclick="selectQuiz('sejarah')">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-purple-50 rounded-xl flex items-center justify-center text-2xl md:text-3xl flex-shrink-0">🏛️</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">Sejarah</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">Jelajahi peristiwa bersejarah dan tokoh-tokoh penting dunia</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <span>📊</span>
                                        <span>20 Soal</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>⏱️</span>
                                        <span>25 menit</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>🏆</span>
                                        <span>120 poin</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:flex-col md:items-end md:text-right space-x-4 md:space-x-0 md:space-y-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-yellow-400 text-sm">⭐⭐⭐⭐</span>
                            </div>
                            <button class="bg-purple-500 hover:bg-purple-600 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                Mulai Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sains -->
                <div class="bg-white rounded-2xl p-4 md:p-6 hover:shadow-xl subtle-hover transition-all duration-300 cursor-pointer border border-gray-100" onclick="selectQuiz('sains')">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-50 rounded-xl flex items-center justify-center text-2xl md:text-3xl flex-shrink-0">🔬</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">Sains</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">Eksplorasi dunia sains dari fisika, kimia, hingga biologi</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <span>📊</span>
                                        <span>30 Soal</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>⏱️</span>
                                        <span>35 menit</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>🏆</span>
                                        <span>150 poin</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:flex-col md:items-end md:text-right space-x-4 md:space-x-0 md:space-y-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-yellow-400 text-sm">⭐⭐⭐⭐⭐</span>
                            </div>
                            <button class="bg-green-500 hover:bg-green-600 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                Mulai Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Olahraga -->
                <div class="bg-white rounded-2xl p-4 md:p-6 hover:shadow-xl subtle-hover transition-all duration-300 cursor-pointer border border-gray-100" onclick="selectQuiz('olahraga')">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-orange-50 rounded-xl flex items-center justify-center text-2xl md:text-3xl flex-shrink-0">⚽</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">Olahraga</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">Tes pengetahuanmu tentang berbagai cabang olahraga dunia</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <span>📊</span>
                                        <span>20 Soal</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>⏱️</span>
                                        <span>20 menit</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>🏆</span>
                                        <span>80 poin</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:flex-col md:items-end md:text-right space-x-4 md:space-x-0 md:space-y-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-yellow-400 text-sm">⭐⭐</span>
                            </div>
                            <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                Mulai Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hiburan -->
                <div class="bg-white rounded-2xl p-4 md:p-6 hover:shadow-xl subtle-hover transition-all duration-300 cursor-pointer border border-gray-100" onclick="selectQuiz('hiburan')">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-pink-50 rounded-xl flex items-center justify-center text-2xl md:text-3xl flex-shrink-0">🎬</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">Hiburan</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">Film, musik, selebriti, dan dunia hiburan populer</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <span>📊</span>
                                        <span>25 Soal</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>⏱️</span>
                                        <span>25 menit</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>🏆</span>
                                        <span>90 poin</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:flex-col md:items-end md:text-right space-x-4 md:space-x-0 md:space-y-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-yellow-400 text-sm">⭐⭐</span>
                            </div>
                            <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                Mulai Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Teknologi -->
                <div class="bg-white rounded-2xl p-4 md:p-6 hover:shadow-xl subtle-hover transition-all duration-300 cursor-pointer border border-gray-100" onclick="selectQuiz('teknologi')">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-start space-x-3 md:space-x-4 flex-1">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-indigo-50 rounded-xl flex items-center justify-center text-2xl md:text-3xl flex-shrink-0">💻</div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-1">Teknologi</h3>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">Dunia digital, gadget, dan perkembangan teknologi terkini</p>
                                <div class="flex flex-wrap gap-2 md:gap-4 text-xs text-gray-500">
                                    <span class="flex items-center space-x-1">
                                        <span>📊</span>
                                        <span>25 Soal</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>⏱️</span>
                                        <span>30 menit</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <span>🏆</span>
                                        <span>110 poin</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between md:flex-col md:items-end md:text-right space-x-4 md:space-x-0 md:space-y-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-yellow-400 text-sm">⭐⭐⭐</span>
                            </div>
                            <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                                Mulai Quiz
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">📊 Statistik Hari Ini</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">1,247</div>
                        <div class="text-gray-600 text-sm">Quiz Dimainkan</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">856</div>
                        <div class="text-gray-600 text-sm">Pemain Aktif</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">23</div>
                        <div class="text-gray-600 text-sm">Room Aktif</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">94%</div>
                        <div class="text-gray-600 text-sm">Tingkat Kepuasan</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black/20 backdrop-blur-sm text-white py-8 mt-auto">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold candy-font mb-4">Candy Quiz</h3>
                    <p class="text-sm opacity-80">Platform quiz interaktif terbaik untuk menguji pengetahuan dan bersenang-senang bersama teman.</p>
                </div>

                <div>
                    <h4 class="font-semibold mb-3">Fitur</h4>
                    <ul class="space-y-2 text-sm opacity-80">
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Quiz Solo</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Multiplayer Room</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Leaderboard</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Achievement</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-3">Kategori</h4>
                    <ul class="space-y-2 text-sm opacity-80">
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Pengetahuan Umum</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Sejarah</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Sains</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Olahraga</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-3">Bantuan</h4>
                    <ul class="space-y-2 text-sm opacity-80">
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Cara Bermain</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Kontak</a></li>
                        <li><a href="#" class="hover:text-blue-200 transition-colors">Tentang Kami</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/20 mt-8 pt-6 text-center">
                <p class="text-sm opacity-70">&copy; 2024 Candy Quiz. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Join Room -->
    <div id="joinModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform scale-95 transition-all duration-300">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 candy-font">Gabung Room Quiz</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Kode Room</label>
                    <input type="text" id="roomCode" placeholder="Masukkan kode room..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-pink-500 focus:outline-none text-center text-xl font-bold tracking-wider">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nama Kamu</label>
                    <input type="text" id="playerName" placeholder="Masukkan nama..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-pink-500 focus:outline-none">
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="joinQuizRoom()" class="flex-1 bg-blue-500 text-white py-3 rounded-xl font-medium hover:bg-blue-600 transition-colors">
                    Gabung Room
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Login -->
    <div id="loginModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform scale-95 transition-all duration-300">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 candy-font">Masuk ke Akun</h2>
                <p class="text-gray-600 mt-2">Selamat datang kembali!</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" id="loginEmail" placeholder="nama@email.com" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" id="loginPassword" placeholder="Masukkan password..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2">
                        <span class="text-gray-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-blue-500 hover:text-blue-600">Lupa password?</a>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button onclick="closeLoginModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="loginUser()" class="flex-1 bg-blue-500 text-white py-3 rounded-xl font-medium hover:bg-blue-600 transition-colors">
                    Masuk
                </button>
            </div>

            <div class="text-center mt-4">
                <span class="text-gray-600">Belum punya akun? </span>
                <button onclick="switchToRegister()" class="text-blue-500 hover:text-blue-600 font-medium">Daftar di sini</button>
            </div>
        </div>
    </div>

    <!-- Modal Register -->
    <div id="registerModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform scale-95 transition-all duration-300">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 candy-font">Buat Akun Baru</h2>
                <p class="text-gray-600 mt-2">Bergabunglah dengan Candy Quiz!</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                    <input type="text" id="registerName" placeholder="Masukkan nama lengkap..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" id="registerEmail" placeholder="nama@email.com" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" id="registerPassword" placeholder="Minimal 6 karakter..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Tipe Akun</label>
                    <select id="userType" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                        <option value="student">Siswa</option>
                        <option value="teacher">Guru</option>
                    </select>
                </div>

                <div class="text-sm">
                    <label class="flex items-start">
                        <input type="checkbox" class="mr-2 mt-1">
                        <span class="text-gray-600">Saya setuju dengan <a href="#" class="text-blue-500 hover:text-blue-600">syarat dan ketentuan</a> yang berlaku</span>
                    </label>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button onclick="closeRegisterModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="registerUser()" class="flex-1 bg-green-500 text-white py-3 rounded-xl font-medium hover:bg-green-600 transition-colors">
                    Daftar
                </button>
            </div>

            <div class="text-center mt-4">
                <span class="text-gray-600">Sudah punya akun? </span>
                <button onclick="switchToLogin()" class="text-blue-500 hover:text-blue-600 font-medium">Masuk di sini</button>
            </div>
        </div>
    </div>

    <!-- Modal Leaderboard -->
    <div id="leaderboardModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-4 md:p-8 max-w-2xl w-full transform scale-95 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800 candy-font">🏆 Leaderboard</h2>
                <p class="text-gray-600 mt-2">Top 10 Pemain Terbaik</p>
            </div>

            <!-- Filter Tabs -->
            <div class="flex justify-center mb-6">
                <div class="bg-gray-100 rounded-lg p-1 flex">
                    <button onclick="switchLeaderboard('weekly')" id="weeklyTab" class="px-4 py-2 rounded-md font-medium transition-colors bg-blue-500 text-white">
                        Mingguan
                    </button>
                    <button onclick="switchLeaderboard('monthly')" id="monthlyTab" class="px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-800">
                        Bulanan
                    </button>
                    <button onclick="switchLeaderboard('alltime')" id="alltimeTab" class="px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-800">
                        Sepanjang Masa
                    </button>
                </div>
            </div>

            <!-- Leaderboard Content -->
            <div id="leaderboardContent">
                <!-- Top 3 Podium -->
                <div class="flex justify-center items-end mb-8 space-x-2 md:space-x-4">
                    <!-- 2nd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-2xl mb-2">🥈</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800 text-sm md:text-base">Sarah K.</div>
                            <div class="text-xs md:text-sm text-gray-600">2,450 poin</div>
                            <div class="text-xs text-gray-500">Level 12</div>
                        </div>
                    </div>

                    <!-- 1st Place -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center text-3xl mb-2">👑</div>
                        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-3 md:p-4 min-w-[90px] md:min-w-[120px]">
                            <div class="font-bold text-gray-800">Alex M.</div>
                            <div class="text-sm text-gray-600">3,280 poin</div>
                            <div class="text-xs text-gray-500">Level 15</div>
                        </div>
                    </div>

                    <!-- 3rd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-400 rounded-full flex items-center justify-center text-2xl mb-2">🥉</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800">Budi S.</div>
                            <div class="text-sm text-gray-600">2,180 poin</div>
                            <div class="text-xs text-gray-500">Level 11</div>
                        </div>
                    </div>
                </div>

                <!-- Ranking List -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                            <div>
                                <div class="font-semibold text-gray-800">Maya R.</div>
                                <div class="text-sm text-gray-600">Level 10</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,950 poin</div>
                            <div class="text-xs text-green-600">+120 hari ini</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">5</div>
                            <div>
                                <div class="font-semibold text-gray-800">Andi P.</div>
                                <div class="text-sm text-gray-600">Level 9</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,720 poin</div>
                            <div class="text-xs text-green-600">+85 hari ini</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">6</div>
                            <div>
                                <div class="font-semibold text-gray-800">Lisa W.</div>
                                <div class="text-sm text-gray-600">Level 8</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,540 poin</div>
                            <div class="text-xs text-green-600">+95 hari ini</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">7</div>
                            <div>
                                <div class="font-semibold text-gray-800">Riko T.</div>
                                <div class="text-sm text-gray-600">Level 8</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,380 poin</div>
                            <div class="text-xs text-green-600">+70 hari ini</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">8</div>
                            <div>
                                <div class="font-semibold text-gray-800">Nina K.</div>
                                <div class="text-sm text-gray-600">Level 7</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,250 poin</div>
                            <div class="text-xs text-green-600">+55 hari ini</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">9</div>
                            <div>
                                <div class="font-semibold text-gray-800">Doni A.</div>
                                <div class="text-sm text-gray-600">Level 7</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,120 poin</div>
                            <div class="text-xs text-green-600">+40 hari ini</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">10</div>
                            <div>
                                <div class="font-semibold text-gray-800">Sari L.</div>
                                <div class="text-sm text-gray-600">Level 6</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">980 poin</div>
                            <div class="text-xs text-green-600">+30 hari ini</div>
                        </div>
                    </div>
                </div>

                <!-- Your Rank -->
                <div class="mt-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <div class="text-center">
                        <div class="text-sm text-blue-600 font-medium mb-1">Peringkat Kamu</div>
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">23</div>
                                <div>
                                    <div class="font-semibold text-gray-800">Kamu</div>
                                    <div class="text-sm text-gray-600">Level 5</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800">650 poin</div>
                                <div class="text-xs text-green-600">+25 hari ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-6">
                <button onclick="closeLeaderboardModal()" class="bg-gray-500 text-white px-6 py-3 rounded-xl font-medium hover:bg-gray-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Create Room -->
    <div id="createModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform scale-95 transition-all duration-300">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 candy-font">Buat Room Quiz</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Nama Room</label>
                    <input type="text" id="roomName" placeholder="Quiz Seru Hari Ini..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Kategori Quiz</label>
                    <select id="quizCategory" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                        <option value="umum">Pengetahuan Umum</option>
                        <option value="sejarah">Sejarah</option>
                        <option value="sains">Sains</option>
                        <option value="olahraga">Olahraga</option>
                        <option value="hiburan">Hiburan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Jumlah Soal</label>
                    <select id="questionCount" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:outline-none">
                        <option value="10">10 Soal</option>
                        <option value="15">15 Soal</option>
                        <option value="20">20 Soal</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-3 mt-6">
                <button onclick="closeCreateModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="createQuizRoom()" class="flex-1 bg-green-500 text-white py-3 rounded-xl font-medium hover:bg-green-600 transition-colors">
                    Buat Room
                </button>
            </div>
        </div>
    </div>

    <script>
        // Page Navigation Functions
        function showHomePage() {
            document.getElementById('homePage').classList.remove('hidden');
            document.getElementById('quizPage').classList.add('hidden');
        }

        function showQuizPage() {
            document.getElementById('homePage').classList.add('hidden');
            document.getElementById('quizPage').classList.remove('hidden');
        }

        function selectQuiz(category) {
            const categoryNames = {
                'umum': 'Pengetahuan Umum',
                'sejarah': 'Sejarah',
                'sains': 'Sains',
                'olahraga': 'Olahraga',
                'hiburan': 'Hiburan',
                'teknologi': 'Teknologi'
            };

            const categoryEmojis = {
                'umum': '🧠',
                'sejarah': '🏛️',
                'sains': '🔬',
                'olahraga': '⚽',
                'hiburan': '🎬',
                'teknologi': '💻'
            };

            const categoryName = categoryNames[category];
            const emoji = categoryEmojis[category];

            alert(`${emoji} Quiz "${categoryName}" dimulai!\n\nSelamat bermain dan semoga berhasil!\n\n🎯 Tips: Baca soal dengan teliti dan jangan terburu-buru!`);
        }

        function joinRoom() {
            document.getElementById('joinModal').classList.remove('hidden');
            document.getElementById('joinModal').classList.add('flex');
            document.getElementById('roomCode').focus();
        }

        function createRoom() {
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('flex');
            document.getElementById('roomName').focus();
        }

        function closeModal() {
            document.getElementById('joinModal').classList.add('hidden');
            document.getElementById('joinModal').classList.remove('flex');
            document.getElementById('roomCode').value = '';
            document.getElementById('playerName').value = '';
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.getElementById('createModal').classList.remove('flex');
            document.getElementById('roomName').value = '';
        }

        function joinQuizRoom() {
            const roomCode = document.getElementById('roomCode').value.trim();
            const playerName = document.getElementById('playerName').value.trim();

            if (!roomCode || !playerName) {
                alert('Mohon isi kode room dan nama kamu!');
                return;
            }

            // Simulasi join room
            closeModal();
            alert(`Berhasil bergabung ke room "${roomCode}"!\n\nHalo ${playerName}, selamat datang!`);
        }

        function createQuizRoom() {
            const roomName = document.getElementById('roomName').value.trim();
            const category = document.getElementById('quizCategory').value;
            const questionCount = document.getElementById('questionCount').value;

            if (!roomName) {
                alert('Mohon isi nama room!');
                return;
            }

            // Generate random room code
            const roomCode = Math.random().toString(36).substring(2, 8).toUpperCase();

            closeCreateModal();
            alert(`Room "${roomName}" berhasil dibuat!\n\nKode Room: ${roomCode}\nKategori: ${category}\nJumlah Soal: ${questionCount}\n\nBagikan kode room kepada teman-temanmu!`);
        }

        // Leaderboard Functions
        function showLeaderboard() {
            document.getElementById('leaderboardModal').classList.remove('hidden');
            document.getElementById('leaderboardModal').classList.add('flex');
        }

        function closeLeaderboardModal() {
            document.getElementById('leaderboardModal').classList.add('hidden');
            document.getElementById('leaderboardModal').classList.remove('flex');
        }

        function switchLeaderboard(type) {
            // Reset all tabs
            document.getElementById('weeklyTab').className = 'px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-800';
            document.getElementById('monthlyTab').className = 'px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-800';
            document.getElementById('alltimeTab').className = 'px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-800';

            // Activate selected tab
            document.getElementById(type + 'Tab').className = 'px-4 py-2 rounded-md font-medium transition-colors bg-blue-500 text-white';

            // Update content based on type
            let content = '';
            if (type === 'weekly') {
                content = getWeeklyLeaderboard();
            } else if (type === 'monthly') {
                content = getMonthlyLeaderboard();
            } else {
                content = getAllTimeLeaderboard();
            }

            document.getElementById('leaderboardContent').innerHTML = content;
        }

        function getWeeklyLeaderboard() {
            return `
                <!-- Top 3 Podium -->
                <div class="flex justify-center items-end mb-8 space-x-2 md:space-x-4">
                    <!-- 2nd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-2xl mb-2">🥈</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800 text-sm md:text-base">Sarah K.</div>
                            <div class="text-xs md:text-sm text-gray-600">2,450 poin</div>
                            <div class="text-xs text-gray-500">Level 12</div>
                        </div>
                    </div>
                    
                    <!-- 1st Place -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center text-3xl mb-2">👑</div>
                        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-3 md:p-4 min-w-[90px] md:min-w-[120px]">
                            <div class="font-bold text-gray-800">Alex M.</div>
                            <div class="text-sm text-gray-600">3,280 poin</div>
                            <div class="text-xs text-gray-500">Level 15</div>
                        </div>
                    </div>
                    
                    <!-- 3rd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-400 rounded-full flex items-center justify-center text-2xl mb-2">🥉</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800">Budi S.</div>
                            <div class="text-sm text-gray-600">2,180 poin</div>
                            <div class="text-xs text-gray-500">Level 11</div>
                        </div>
                    </div>
                </div>
                
                <!-- Ranking List -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                            <div>
                                <div class="font-semibold text-gray-800">Maya R.</div>
                                <div class="text-sm text-gray-600">Level 10</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,950 poin</div>
                            <div class="text-xs text-green-600">+120 minggu ini</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">5</div>
                            <div>
                                <div class="font-semibold text-gray-800">Andi P.</div>
                                <div class="text-sm text-gray-600">Level 9</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-800">1,720 poin</div>
                            <div class="text-xs text-green-600">+85 minggu ini</div>
                        </div>
                    </div>
                </div>
                
                <!-- Your Rank -->
                <div class="mt-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <div class="text-center">
                        <div class="text-sm text-blue-600 font-medium mb-1">Peringkat Kamu Minggu Ini</div>
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">15</div>
                                <div>
                                    <div class="font-semibold text-gray-800">Kamu</div>
                                    <div class="text-sm text-gray-600">Level 5</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800">450 poin</div>
                                <div class="text-xs text-green-600">+180 minggu ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function getMonthlyLeaderboard() {
            return `
                <!-- Top 3 Podium -->
                <div class="flex justify-center items-end mb-8 space-x-2 md:space-x-4">
                    <!-- 2nd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-2xl mb-2">🥈</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800">Maya R.</div>
                            <div class="text-sm text-gray-600">8,750 poin</div>
                            <div class="text-xs text-gray-500">Level 18</div>
                        </div>
                    </div>
                    
                    <!-- 1st Place -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center text-3xl mb-2">👑</div>
                        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-3 md:p-4 min-w-[90px] md:min-w-[120px]">
                            <div class="font-bold text-gray-800">Alex M.</div>
                            <div class="text-sm text-gray-600">12,480 poin</div>
                            <div class="text-xs text-gray-500">Level 22</div>
                        </div>
                    </div>
                    
                    <!-- 3rd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-400 rounded-full flex items-center justify-center text-2xl mb-2">🥉</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800">Lisa W.</div>
                            <div class="text-sm text-gray-600">7,920 poin</div>
                            <div class="text-xs text-gray-500">Level 16</div>
                        </div>
                    </div>
                </div>
                
                <!-- Your Rank -->
                <div class="mt-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <div class="text-center">
                        <div class="text-sm text-blue-600 font-medium mb-1">Peringkat Kamu Bulan Ini</div>
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">28</div>
                                <div>
                                    <div class="font-semibold text-gray-800">Kamu</div>
                                    <div class="text-sm text-gray-600">Level 5</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800">1,850 poin</div>
                                <div class="text-xs text-green-600">+720 bulan ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function getAllTimeLeaderboard() {
            return `
                <!-- Top 3 Podium -->
                <div class="flex justify-center items-end mb-8 space-x-2 md:space-x-4">
                    <!-- 2nd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-2xl mb-2">🥈</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800">Maya R.</div>
                            <div class="text-sm text-gray-600">45,280 poin</div>
                            <div class="text-xs text-gray-500">Level 35</div>
                        </div>
                    </div>
                    
                    <!-- 1st Place -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center text-3xl mb-2">👑</div>
                        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-3 md:p-4 min-w-[90px] md:min-w-[120px]">
                            <div class="font-bold text-gray-800">Alex M.</div>
                            <div class="text-sm text-gray-600">67,950 poin</div>
                            <div class="text-xs text-gray-500">Level 42</div>
                        </div>
                    </div>
                    
                    <!-- 3rd Place -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-400 rounded-full flex items-center justify-center text-2xl mb-2">🥉</div>
                        <div class="bg-gray-100 rounded-lg p-2 md:p-3 min-w-[80px] md:min-w-[100px]">
                            <div class="font-bold text-gray-800">Sarah K.</div>
                            <div class="text-sm text-gray-600">38,750 poin</div>
                            <div class="text-xs text-gray-500">Level 31</div>
                        </div>
                    </div>
                </div>
                
                <!-- Your Rank -->
                <div class="mt-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <div class="text-center">
                        <div class="text-sm text-blue-600 font-medium mb-1">Peringkat Kamu Sepanjang Masa</div>
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">156</div>
                                <div>
                                    <div class="font-semibold text-gray-800">Kamu</div>
                                    <div class="text-sm text-gray-600">Level 5</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-800">2,450 poin</div>
                                <div class="text-xs text-blue-600">Total sepanjang masa</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Login & Register Functions
        function showLogin() {
            document.getElementById('loginModal').classList.remove('hidden');
            document.getElementById('loginModal').classList.add('flex');
            document.getElementById('loginEmail').focus();
        }

        function showRegister() {
            document.getElementById('registerModal').classList.remove('hidden');
            document.getElementById('registerModal').classList.add('flex');
            document.getElementById('registerName').focus();
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
            document.getElementById('loginModal').classList.remove('flex');
            document.getElementById('loginEmail').value = '';
            document.getElementById('loginPassword').value = '';
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').classList.add('hidden');
            document.getElementById('registerModal').classList.remove('flex');
            document.getElementById('registerName').value = '';
            document.getElementById('registerEmail').value = '';
            document.getElementById('registerPassword').value = '';
        }

        function switchToRegister() {
            closeLoginModal();
            showRegister();
        }

        function switchToLogin() {
            closeRegisterModal();
            showLogin();
        }

        function loginUser() {
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value.trim();

            if (!email || !password) {
                alert('Mohon isi email dan password!');
                return;
            }

            // Simulasi login
            closeLoginModal();
            alert(`Selamat datang kembali!\n\nAnda berhasil masuk dengan email: ${email}`);
        }

        function registerUser() {
            const name = document.getElementById('registerName').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value.trim();
            const userType = document.getElementById('userType').value;

            if (!name || !email || !password) {
                alert('Mohon isi semua field yang diperlukan!');
                return;
            }

            if (password.length < 6) {
                alert('Password minimal 6 karakter!');
                return;
            }

            // Simulasi register
            closeRegisterModal();
            const userTypeText = userType === 'teacher' ? 'Guru' : 'Siswa';
            alert(`Selamat datang ${name}!\n\nAkun ${userTypeText} berhasil dibuat dengan email: ${email}\n\nSilakan masuk untuk mulai menggunakan Candy Quiz!`);
        }

        // Close modal when clicking outside
        document.getElementById('joinModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('createModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateModal();
            }
        });

        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        document.getElementById('registerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRegisterModal();
            }
        });

        document.getElementById('leaderboardModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLeaderboardModal();
            }
        });

        // Mobile Menu Functions
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        }

        function closeMobileMenu() {
            document.getElementById('mobileMenu').classList.add('hidden');
        }

        // Enter key functionality
        document.getElementById('roomCode').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('playerName').focus();
            }
        });

        document.getElementById('playerName').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                joinQuizRoom();
            }
        });

        document.getElementById('roomName').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                createQuizRoom();
            }
        });
    </script>
    <script>
        (function() {
            function c() {
                var b = a.contentDocument || a.contentWindow.document;
                if (b) {
                    var d = b.createElement('script');
                    d.innerHTML = "window.__CF$cv$params={r:'97aebbd6f7726045',t:'MTc1NzE2OTc1NS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";
                    b.getElementsByTagName('head')[0].appendChild(d)
                }
            }
            if (document.body) {
                var a = document.createElement('iframe');
                a.height = 1;
                a.width = 1;
                a.style.position = 'absolute';
                a.style.top = 0;
                a.style.left = 0;
                a.style.border = 'none';
                a.style.visibility = 'hidden';
                document.body.appendChild(a);
                if ('loading' !== document.readyState) c();
                else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c);
                else {
                    var e = document.onreadystatechange || function() {};
                    document.onreadystatechange = function(b) {
                        e(b);
                        'loading' !== document.readyState && (document.onreadystatechange = e, c())
                    }
                }
            }
        })();
    </script>
</body>

</html>