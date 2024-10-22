@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Slide</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.slides')}}">
                        <div class="text-tiny">Slides</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Slide</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.slide.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Tagline -->
                <fieldset class="name">
                    <div class="body-title">Tagline <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Tagline" name="tagline" value="{{old('tagline')}}" required>
                    @if($errors->has('tagline'))
                        <div class="error">{{ $errors->first('tagline') }}</div>
                    @endif
                </fieldset>

                <!-- Title -->
                <fieldset class="name">
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Title" name="title" value="{{old('title')}}" required>
                    @if($errors->has('title'))
                        <div class="error">{{ $errors->first('title') }}</div>
                    @endif
                </fieldset>

                <!-- Subtitle -->
                <fieldset class="name">
                    <div class="body-title">Subtitle <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Subtitle" name="subtitle" value="{{old('subtitle')}}" required>
                    @if($errors->has('subtitle'))
                        <div class="error">{{ $errors->first('subtitle') }}</div>
                    @endif
                </fieldset>

                <!-- Link -->
                <fieldset class="name">
                    <div class="body-title">Link <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Link" name="link" tabindex="0" value="{{old('link')}}" aria-required="true" required="">
                    @if($errors->has('link'))
                        <div class="error">{{ $errors->first('link') }}</div>
                    @endif
                </fieldset>

                

                <!-- Status -->
                <fieldset class="category">
                    <div class="body-title">Status</div>
                    <div class="select flex-grow">
                        <select name="status" >
                            <option value="">Select</option>
                            <option value="1" @if(old('status')=="1") selected @endif>Active</option>
                            <option value="0" @if(old('status')=="0") selected @endif>Inactive</option>
                        </select>
                    </div>
                    @if($errors->has('status'))
                        <div class="error">{{ $errors->first('status') }}</div>
                    @endif
                </fieldset>

                <!-- Submit Button -->
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
        <!-- /new-category -->
    </div>
    <!-- /main-content-wrap -->
</div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#myFile").on("change", function(e) {
                const photoInp = $("#myFile");
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                } 
            });
        });
    </script>
@endpush
