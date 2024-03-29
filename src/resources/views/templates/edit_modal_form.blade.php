<div 
    class="modal fade" 
    id="editModal" 
    tabindex="-1" 
    role="dialog" 
    aria-labelledby="editModal" 
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$title}}</h5>
                <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form 
                      method="{{$formMethod}}"
                      action=""
                      enctype="multipart/form-data"
                >
                    @csrf
                    <div class='modal-form'>
                        @if(isset($isFileAvailable) && $isFileAvailable)
                            <div class="col-1 p-0">
                                <img class="w-100" id="icon" src="" alt="">
                            </div>
                            <div class="form-group">
                                <div style="height:30px">
                                    <label for="image">File</label>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image" id="image">
                                    <label class="custom-file-label" for="customFile" id="fileLabel">Choose file</label>
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <label class="required">{{ucfirst($labelOne)}}</label>
                        <div class="form-group">
                            <input 
                                type='text' 
                                name='{{$inputOne}}'
                                id='{{$inputOne}}'
                                class='form-control' 
                                value='{{ old($inputOne) ? e(old($inputOne)) : '' }}'
                                required
                                />
                            <span id="{{$labelOne}}" class="d-none text-danger"> </span>
                        </div>
                        <label class="required">{{ucfirst($labelTwo)}}</label>
                        <div class="form-group">
                            <textarea 
                                class='form-control' 
                                value='{{ old($inputTwo) ? e(old($inputTwo)) : '' }}'
                                rows='2' 
                                cols='2' 
                                id='{{$inputTwo}}' 
                                name='{{$inputTwo}}'
                            ></textarea>
                            <span id="{{$inputTwo}}" class="d-none text-danger"> </span>
                        </div>
                        @if($isAvailableMultiple)
                            <div class="form-group">
                                <label>Assign subcategories</label>
                                <select 
                                    multiple="" 
                                    class="form-control selectSubCategory" 
                                    name="sub_categories[]"
                                    multiple data-selected-text-format="count > 5" 
                                    data-actions-box="true" 
                                    data-dropup-auto="false" 
                                >
                                </select>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class='row w-100'>
                    <div class='col-12'>
                        <button type="button" class="btn btn-secondary modalCloseBtn" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateForm">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>