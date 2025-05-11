<div class="report-filter">
    <p>@lang('minireportb1::minireportb1.username')</p>
    <div class="filter-group">
        <select class="filter-select" name="username_filter" id="username_filter">
            <option value="">@lang('minireportb1::minireportb1.all_users')</option>
            @if (isset($users))
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ request()->get('username_filter') == $user->id ? 'selected' : '' }}>
                        {{ $user->full_name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>