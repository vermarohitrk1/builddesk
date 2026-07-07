{{-- 
    Page Header Component 
    Usage:
    <x-ui.page-header title="Page Title" :breadcrumbs="['Home' => route('home'), 'Current' => null]">
        <button class="btn btn-primary">Add New</button>
    </x-ui.page-header>
--}}
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="page-title-left">
        @if(isset($title))
            <h4 class="mb-1 fw-bold">{{ $title }}</h4>
        @endif
        
        @if(isset($breadcrumbs) && is_array($breadcrumbs))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mb-sm-0" style="font-size: 0.85rem;">
                    @foreach($breadcrumbs as $label => $url)
                        @if(!$loop->last && $url)
                            <li class="breadcrumb-item"><a href="{{ $url }}" class="text-decoration-none">{{ $label }}</a></li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif
    </div>

    @if(isset($slot) && $slot->isNotEmpty())
        <div class="page-header-actions d-flex gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
