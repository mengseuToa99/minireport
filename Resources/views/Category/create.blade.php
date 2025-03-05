<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('minireportb1::lang.add_category')</h4>
        </div>
        <div class="modal-body">
            <form id="category_add_form" method="POST" action="{{ route('MiniReportB1-categories.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">@lang('minireportb1::lang.name'):</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">@lang('minireportb1::lang.description'):</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            </form>
        </div>
    </div>
</div>
