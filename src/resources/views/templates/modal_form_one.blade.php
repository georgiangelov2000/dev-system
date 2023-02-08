<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalForm" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{$title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form 
                      method="{{$formMethod}}"
                      action="{{$formRoute}}"
                      id="{{$formIdentificator}}"
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
                                required
                                />
                        </div>
                        <label class="required">{{ucfirst($labelTwo)}}</label>
                        <div class="form-group">
                            <textarea 
                                class='form-control' 
                                rows='2' 
                                cols='2' 
                                id='{{$inputTwo}}' 
                                name='{{$inputTwo}}'
                            ></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class='row w-100'>
                    @if($isMultiple)
                    <div class='col-5 p-0'>
                        <button type="button" class='btn btn-warning' id='addModalForm'>
                            Add More
                        </button>
                        <button type="button" class='btn btn-danger d-none' id='removeModalForm'/>
                        Remove
                        </button>
                    </div>

                    @endif
                    <div class='col-7 text-right p-0'>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitForm">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  