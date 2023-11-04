<div class="content-header p-0">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-sm-10">
                <h1 class="m-0">{{ $company_information['name'] ?? '' }}</h1>
            </div>
            <div class="col-sm-2 text-right">
                <img class="img-fluid w-25" src="{{ $company_information['image_path'] ?? '' }}" alt="">
            </div>
        </div>
    </div>
</div>
