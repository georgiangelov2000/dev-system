<div 
    class="modal fade" 
    id="editModal" 
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
                <form method="{{$formMethod}}" action="">
                    @method('PUT')
                    @csrf
                    <div class='modal-form'>
                        <label class="required">{{ucfirst($labelOne)}}</label>
                        <div class={{$isDatePicker ? 'input-group' : 'form-group'}}>
                            @if($isDatePicker)
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                            @endif
                            <input 
                            type='{{ $inputType }}' 
                            name='{{ $inputOne }}'
                            id='{{ $inputOne }}'
                            class='{{ $isDatePicker ? "form-control datepicker" : "form-control" }}' 
                            value=''
                            required/>                        
                            <span id="{{$labelOne}}" class="d-none text-danger"> </span>
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