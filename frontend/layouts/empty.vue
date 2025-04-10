<template>
    <div
        class="min-h-screen flex items-center justify-center p-0 lg:p-4 bg-gradient-to-br from-green-50 to-green-100 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
            <div class="absolute top-10 left-10 w-32 h-32 bg-green-300 rounded-full opacity-30 animate-blob"></div>
            <div
                class="absolute bottom-20 right-20 w-48 h-48 bg-green-400 rounded-full opacity-20 animate-blob animation-delay-2000">
            </div>
        </div>
        <slot/>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0, 0) scale(1);
    }

    33% {
        transform: translate(30px, -50px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    100% {
        transform: translate(0, 0) scale(1);
    }
}

.animate-blob {
    animation: blob 10s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
</style>