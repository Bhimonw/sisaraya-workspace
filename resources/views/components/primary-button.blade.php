<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-violet-600 to-blue-600 border border-transparent rounded-full font-semibold text-sm text-white uppercase tracking-wide hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 active:scale-95 transition-all duration-300']) }}>
    {{ $slot }}
</button>
