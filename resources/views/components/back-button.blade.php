<div>
    <a href="{{ url()->previous() }}"
        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium mb-4">
         <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
         </svg>
         <span>{{ $label }}</span>
     </a>
</div>
