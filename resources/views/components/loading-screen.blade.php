@props(['message' => 'Processing...'])

<div id="loadingScreen" style="display: none;" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
    <div class="text-center">
        <!-- Animated Logo (Sailing Boat) -->
        <div class="relative mb-6">
            <div class="wave-container">
                <img src="{{ asset('images/baltbep-logo.png') }}" alt="BaltBep Logo" class="sailing-boat w-32 h-32 mx-auto">
                <div class="wave wave1"></div>
                <div class="wave wave2"></div>
                <div class="wave wave3"></div>
            </div>
        </div>
        
        <!-- Loading Message -->
        <h2 class="text-xl font-semibold text-gray-800 mb-2" id="loadingMessage">{{ $message }}</h2>
        <p class="text-gray-600">Please wait a moment...</p>
        
        <!-- Loading Dots Animation -->
        <div class="flex justify-center mt-4 space-x-2">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
</div>

<style>
    .wave-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }
    
    .sailing-boat {
        position: relative;
        z-index: 10;
        animation: sail 3s ease-in-out infinite;
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
    }
    
    @keyframes sail {
        0%, 100% {
            transform: translateY(0) rotate(-2deg);
        }
        50% {
            transform: translateY(-15px) rotate(2deg);
        }
    }
    
    .wave {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 200px;
        height: 40px;
        background: linear-gradient(transparent, rgba(59, 130, 246, 0.3));
        border-radius: 50%;
        animation: wave 2s ease-in-out infinite;
    }
    
    .wave1 {
        animation-delay: 0s;
    }
    
    .wave2 {
        animation-delay: 0.3s;
        opacity: 0.7;
    }
    
    .wave3 {
        animation-delay: 0.6s;
        opacity: 0.5;
    }
    
    @keyframes wave {
        0%, 100% {
            transform: translateX(-50%) scaleX(1);
        }
        50% {
            transform: translateX(-50%) scaleX(1.2);
        }
    }
    
    .dot {
        width: 12px;
        height: 12px;
        background-color: #3b82f6;
        border-radius: 50%;
        animation: dotPulse 1.5s ease-in-out infinite;
    }
    
    .dot:nth-child(1) {
        animation-delay: 0s;
    }
    
    .dot:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .dot:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes dotPulse {
        0%, 100% {
            transform: scale(0.8);
            opacity: 0.5;
        }
        50% {
            transform: scale(1.2);
            opacity: 1;
        }
    }
</style>

<script>
    // Helper functions to show/hide loading screen
    window.showLoading = function(message) {
        const loadingScreen = document.getElementById('loadingScreen');
        const loadingMessage = document.getElementById('loadingMessage');
        if (message) {
            loadingMessage.textContent = message;
        }
        loadingScreen.style.display = 'flex';
    };
    
    window.hideLoading = function() {
        const loadingScreen = document.getElementById('loadingScreen');
        loadingScreen.style.display = 'none';
    };
</script>
