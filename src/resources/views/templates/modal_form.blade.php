<div 
    class="modal fade" 
    id="createModal" 
    tabindex="-1" 
    role="dialog" 
    aria-labelledby="createModal" 
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
                >
                    @csrf
                    <div class='modal-form'>
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
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class='row w-100'>
                    <div class='col-12'>
                        <button type="button" class="btn btn-secondary modalCloseBtn" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitForm">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  