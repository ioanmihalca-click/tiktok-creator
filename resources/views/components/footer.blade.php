<footer class="w-full py-12 mt-8 text-sm border-t border-white/10 bg-black/30 backdrop-blur-sm">
    <div class="container max-w-6xl px-4 mx-auto">
        <!-- Footer Upper Section -->
        <div class="grid grid-cols-1 gap-8 mb-10 sm:grid-cols-2 md:grid-cols-4">
            <!-- Company Info -->
            <div>
                <img src="{{ asset('assets/logo-transparent.webp') }}" alt="TikTok Creator Logo" class="h-12 mb-4">
                <p class="mb-6 text-gray-400">Transformă-ți ideile în conținut viral folosind puterea inteligenței
                    artificiale. 100% automatizat.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 transition-colors hover:text-purple-400">
                        <i class="text-xl ri-facebook-fill"></i>
                    </a>
                    <a href="#" class="text-gray-400 transition-colors hover:text-purple-400">
                        <i class="text-xl ri-instagram-fill"></i>
                    </a>
                    <a href="#" class="text-gray-400 transition-colors hover:text-purple-400">
                        <i class="text-xl ri-tiktok-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="mb-4 text-lg font-medium text-white">Linkuri Rapide</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 transition-colors hover:text-purple-400">Acasă</a></li>
                    <li><a href="#how-it-works" class="text-gray-400 transition-colors hover:text-purple-400">Cum
                            funcționează</a></li>
                    <li><a href="#pricing" class="text-gray-400 transition-colors hover:text-purple-400">Prețuri</a>
                    </li>
                    <li><a href="{{ route('register') }}"
                            class="text-gray-400 transition-colors hover:text-purple-400">Înregistrare</a></li>
                </ul>
            </div>

            <!-- Legal Links -->
            <div>
                <h4 class="mb-4 text-lg font-medium text-white">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 transition-colors hover:text-purple-400">Termeni și
                            condiții</a></li>
                    <li><a href="#" class="text-gray-400 transition-colors hover:text-purple-400">Politica de
                            confidențialitate</a></li>
                    <li><a href="#" class="text-gray-400 transition-colors hover:text-purple-400">Politica de
                            cookies</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="mb-4 text-lg font-medium text-white">Contact</h4>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-gray-400">
                        <i class="text-purple-400 ri-mail-line"></i>
                        <a href="mailto:contact@tiktok-creator.ro"
                            class="transition-colors hover:text-purple-400">contact@tiktok-creator.ro</a>
                    </li>
                    <li class="flex items-center gap-2 text-gray-400">
                        <i class="text-purple-400 ri-customer-service-2-line"></i>
                        <span>Suport: Luni-Vineri, 9:00-17:00</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom Section -->
        <div class="flex flex-col items-center justify-between pt-8 border-t border-white/10 sm:flex-row">
            <p class="text-gray-400">&copy; {{ date('Y') }} TikTok Creator. Toate drepturile rezervate.</p>
            <p class="mt-3 text-gray-400 sm:mt-0">
                Aplicație dezvoltată de
                <a href="https://clickstudios-digital.com" target="_blank" rel="noopener noreferrer"
                    class="font-medium text-purple-400 transition-colors duration-200 hover:text-purple-300 hover:underline">
                    Click Studios Digital
                </a>
            </p>
        </div>
    </div>
</footer>
