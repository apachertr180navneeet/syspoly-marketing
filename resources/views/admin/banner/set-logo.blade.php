@extends('admin.layouts.app')

@section('style')
<style>
    .banner-container {
        position: relative;
        display: inline-block;
        max-width: 100%;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }

    .banner-container img.banner {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Draggable elements */
    .draggable {
        position: absolute;
        cursor: move;
        user-select: none;
        transition: box-shadow 0.2s;
    }

    .draggable:active {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .logo-overlay {
        max-width: 120px;
        height: auto;
        opacity: 0.9;
    }

    .text-overlay {
        font-size: 22px;
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.6);
    }

    .customer-card {
        display: flex;
        align-items: center;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: 0.3s;
    }

    .customer-card:hover {
        background-color: #f8f9fa;
    }

    .customer-card img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        margin-right: 15px;
    }

    .customer-selected {
        border-color: #007bff;
        background-color: #e7f1ff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Set Customer Logo & Text Position</span>
            </h5>
        </div>
    </div>

    {{-- Banner preview area --}}
    <div class="text-center mb-4">
        <div class="banner-container" id="bannerContainer">
            <img src="{{ asset($banner->image) }}" alt="Banner Image" class="banner" id="bannerImage">

            {{-- Draggable logo --}}
            <img src="{{ asset('uploads/customers/noimg.jpg') }}" 
                 alt="Customer Logo" 
                 class="draggable logo-overlay" 
                 id="logoPreview" 
                 style="bottom: 20px; right: 20px;">

            {{-- Draggable text --}}
            <div class="draggable text-overlay" id="textOverlay" style="bottom: 60px; right: 20px;">
                Sample Text
            </div>
        </div>
    </div>

    {{-- Upload and position form --}}
    <form action="{{ route('admin.banner.logo.update', $banner->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="logo_x" id="logoX">
        <input type="hidden" name="logo_y" id="logoY">
        <input type="hidden" name="text_x" id="textX">
        <input type="hidden" name="text_y" id="textY">

        <button type="submit" class="btn btn-primary">Save Positions</button>
    </form>

    {{-- Customer list --}}
    <div class="mb-4">
        <h6 class="fw-bold">Select a Customer</h6>
        <div class="customer-list row g-3" style="max-height: 300px; overflow-y: auto;">
            @foreach($customers as $customer)
            <div class="customer-card col-2" data-logo="{{ asset($customer->logo ?? 'uploads/customers/noimg.jpg') }}">
                <img src="{{ asset($customer->logo ?? 'uploads/customers/noimg.jpg') }}" alt="Logo">
                <div class="customer-info">
                    <strong>{{ $customer->firm_name }}</strong>
                    <br>
                    <small>{{ $customer->person_name ?? 'N/A' }}</small>
                    <br>
                    <small>{{ $customer->contact_number }}</small>
                    <br>
                    <small>{{ $customer->email }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logo = document.getElementById('logoPreview');
    const text = document.getElementById('textOverlay');
    const textInput = document.getElementById('textInput');
    const customerCards = document.querySelectorAll('.customer-card');

    const logoX = document.getElementById('logoX');
    const logoY = document.getElementById('logoY');
    const textX = document.getElementById('textX');
    const textY = document.getElementById('textY');

    let activeElement = null;
    let offsetX = 0;
    let offsetY = 0;

    function makeDraggable(el) {
        el.addEventListener('mousedown', function(e) {
            activeElement = el;
            const rect = el.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
        });
    }

    function drag(e) {
        if (!activeElement) return;
        const containerRect = document.getElementById('bannerContainer').getBoundingClientRect();
        let left = e.clientX - containerRect.left - offsetX;
        let top = e.clientY - containerRect.top - offsetY;

        // Limit dragging inside the banner
        left = Math.max(0, Math.min(left, containerRect.width - activeElement.offsetWidth));
        top = Math.max(0, Math.min(top, containerRect.height - activeElement.offsetHeight));

        activeElement.style.left = left + 'px';
        activeElement.style.top = top + 'px';
        activeElement.style.right = 'auto';
        activeElement.style.bottom = 'auto';
    }

    function stopDrag() {
        if (activeElement === logo) {
            logoX.value = logo.style.left.replace('px', '');
            logoY.value = logo.style.top.replace('px', '');
        } else if (activeElement === text) {
            textX.value = text.style.left.replace('px', '');
            textY.value = text.style.top.replace('px', '');
        }
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('mouseup', stopDrag);
        activeElement = null;
    }

    makeDraggable(logo);
    makeDraggable(text);

    // Change text live
    textInput.addEventListener('input', function() {
        text.textContent = this.value;
    });

    // Change preview logo on customer select
    customerCards.forEach(card => {
        card.addEventListener('click', function() {
            customerCards.forEach(c => c.classList.remove('customer-selected'));
            this.classList.add('customer-selected');
            logo.src = this.getAttribute('data-logo');
        });
    });
});
</script>
@endsection
