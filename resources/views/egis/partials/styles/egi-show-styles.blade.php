{{-- resources/views/egis/partials/styles/egi-show-styles.blade.php --}}
{{-- 
    Stili CSS personalizzati per la pagina EGI show
    ORIGINE: righe 178-213 di show.blade.php
    VARIABILI: nessuna
--}}

{{-- Custom Styles for Enhanced Interactivity --}}
<style>
    .artwork-container:hover img {
        transform: scale(1.01);
        filter: brightness(1.05) contrast(1.02);
    }

    .like-button.is-liked {
        background: linear-gradient(135deg, rgba(236, 72, 153, 0.3) 0%, rgba(147, 51, 234, 0.3) 100%);
        border-color: rgba(236, 72, 153, 0.5);
    }

    @media (max-width: 1024px) {
        .artwork-container {
            margin-bottom: 2rem;
        }
    }

    /* Scrollbar styling for sidebar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: rgba(55, 65, 81, 0.3);
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.8);
    }
</style>
