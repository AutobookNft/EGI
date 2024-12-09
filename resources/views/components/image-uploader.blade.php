@props([
    'model',
    'id',
    'label',
    'image',
    'removeMethod' => 'removeImage',
])


<div class="items-center form-control">
    <label class="label">
        <span class="font-semibold label-text">{{ $label }}</span>
    </label>

    {{--
        Debug. Vista su url temporaneo dell'immagine caricata
        @if ($this->$model instanceof Illuminate\Http\UploadedFile)
            @dump($this->$model->temporaryUrl())
        @endif
    --}}

    <div class="tooltip" data-tip="Click to upload image">

        <div class="avatar">
            <div class="w-24 border rounded-full cursor-pointer border-base-300 hover:border-primary"
            @if($image === '' || $image === null)
                onclick="document.getElementById('{{ $id }}').click();"
            @endif
            >

                @if($image !== '' && $image !== null )
                    <img src="{{ Storage::url($image) }}" class="object-cover w-full h-full rounded-full" title="{{ $image }}">
                @else
                    @if ($this->$model instanceof Illuminate\Http\UploadedFile)
                        <img src="{{ $this->$model->temporaryUrl() }}" class="object-cover w-full h-full rounded-full">
                    @else
                        <div class="flex items-center justify-center h-full text-base-content">
                            @if ($iconHtml)
                                <div>{!! $iconHtml !!}</div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <input
        type="file"
        wire:model={{ $model }}
        id="{{ $id }}"
        class="hidden"
        accept="image/*"
    >

    @error($model)
        <span class="mt-1 text-sm text-error">messaggio {{ $message }}</span>
    @enderror

    @if ($image !== '' && $image !== null )
        <button
            type="button"
            wire:click="{{ $removeMethod }}( '{{ $id }}' )"
            onclick="document.getElementById('{{ $id }}').value = '';"
            class="mt-3 btn btn-error"
        >
            {{ __('label.delete') }}
        </button>
    @endif
</div>
