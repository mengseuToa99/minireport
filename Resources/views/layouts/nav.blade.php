<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action([\Modules\MiniReportB1\Http\Controllers\MiniReportB1Controller::class, 'dashboard'])}}">
                    <i class="fa fa-envelope-open-text"  style="width: 30px; height: auto; color:#1430ff;" aria-hidden="true"></i>
                    @lang("minireportb1::lang.dashboard")
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- Dashboard link -->
                    <li @if(request()->segment(2) == 'MiniReportB1') class="active" @endif>
                        <a href="{{action([\Modules\MiniReportB1\Http\Controllers\MiniReportB1Controller::class, 'index'])}}">
                            @lang("minireportb1::lang.minireportb1")
                        </a>
                    </li>

                    <!-- Categories link -->
                    <li @if(request()->segment(2) == 'MiniReportB1-categories') class="active" @endif>
                        <a href="{{action([\Modules\MiniReportB1\Http\Controllers\MiniReportB1Controller::class, 'getCategories'])}}">
                            @lang("minireportb1::lang.MiniReportB1_category")
                        </a>
                    </li>

                    <!-- Permission link -->
                    <li @if(request()->segment(2) == 'MiniReportB1-permission') class="active" @endif>
                        <a href="{{action([\Modules\MiniReportB1\Http\Controllers\SettingController::class, 'showMiniReportB1PermissionForm'])}}">
                            @lang("minireportb1::lang.setting")
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>