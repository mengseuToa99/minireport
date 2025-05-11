@if($load_data)
<div id="table-container">
    <!-- U the pagination component instead of custom controls -->
    <div style="max-width: 600px;">
    @include('minireportb1::MiniReportB1.components.pagination')
    </div>

    <table class="financial-table" style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>@lang('minireportb1::minireportb1.employee_name')</th>
                <th>@lang('minireportb1::minireportb1.payroll_month')</th>
                <th>@lang('minireportb1::minireportb1.base_salary')</th>
                @if(request()->get('show_allowances', '1') == '1')
                    <th>@lang('minireportb1::minireportb1.allowances')</th>
                @endif
                @if(request()->get('show_deductions', '1') == '1')
                    <th>@lang('minireportb1::minireportb1.deductions')</th>
                @endif
                <th>@lang('minireportb1::minireportb1.net_salary')</th>
                <th>ការពិពណ៌នា</th>
                <th>@lang('minireportb1::minireportb1.payment_status')</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($payroll_data) && count($payroll_data) > 0)
                @foreach($payroll_data as $data)
                    <tr>
                        <td>{{ $data->name ?? $data->username }}</td>
                        <td>
                            @php
                                // Convert YYYY-MM to Month Year format
                                if(!isset($data->payroll_month) || empty($data->payroll_month)) {
                                    echo 'N/A';
                                } else {
                                    $month_year = explode('-', $data->payroll_month);
                                    if (count($month_year) == 2) {
                                        $month_names = [
                                            '01' => 'January', '02' => 'February', '03' => 'March',
                                            '04' => 'April', '05' => 'May', '06' => 'June',
                                            '07' => 'July', '08' => 'August', '09' => 'September',
                                            '10' => 'October', '11' => 'November', '12' => 'December'
                                        ];
                                        echo isset($month_names[$month_year[1]]) ? $month_names[$month_year[1]] . ' ' . $month_year[0] : $data->payroll_month;
                                    } else {
                                        echo $data->payroll_month;
                                    }
                                }
                            @endphp
                        </td>
                        <td>@format_currency($data->base_salary)</td>
                        @if(request()->get('show_allowances', '1') == '1')
                            <td>@format_currency($data->allowances ?? 0)</td>
                        @endif
                        @if(request()->get('show_deductions', '1') == '1')
                            <td>@format_currency($data->deductions ?? 0)</td>
                        @endif
                        <td>@format_currency($data->net_salary)</td>
                        <td>
                            @if(!empty($data->allowance_descriptions))
                                <div class="description-item">
                                    <strong>@lang('minireportb1::minireportb1.allowances'):</strong>
                                    <div>{{ $data->allowance_descriptions }}</div>
                                </div>
                            @endif
                            
                            @if(!empty($data->deduction_descriptions))
                                <div class="description-item">
                                    <strong>@lang('minireportb1::minireportb1.deductions'):</strong>
                                    <div>{{ $data->deduction_descriptions }}</div>
                                </div>
                            @endif
                            
                            @if(empty($data->allowance_descriptions) && empty($data->deduction_descriptions))
                                N/A
                            @endif
                        </td>
                        <td>
                            @if(isset($data->payment_status))
                                <span class="label 
                                    @if($data->payment_status == 'paid') label-success 
                                    @elseif($data->payment_status == 'No Payroll') label-info 
                                    @else label-warning 
                                    @endif">
                                    {{ $data->payment_status }}
                                </span>
                            @else
                                <span class="label label-default">N/A</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ 6 + (request()->get('show_allowances', '1') == '1' ? 1 : 0) + (request()->get('show_deductions', '1') == '1' ? 1 : 0) }}" class="text-center">
                        @lang('minireportb1::minireportb1.no_data_available')
                    </td>
                </tr>
            @endif
        </tbody>
        @if(isset($payroll_data) && count($payroll_data) > 0)
            <tfoot>
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td colspan="2">@lang('minireportb1::minireportb1.total')</td>
                    <td>@format_currency($totals['base_salary'])</td>
                    @if(request()->get('show_allowances', '1') == '1')
                        <td>@format_currency($totals['allowances'])</td>
                    @endif
                    @if(request()->get('show_deductions', '1') == '1')
                        <td>@format_currency($totals['deductions'])</td>
                    @endif
                    <td>@format_currency($totals['net_salary'])</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

<style>
    .description-item {
        margin-bottom: 8px;
    }
    .description-item:last-child {
        margin-bottom: 0;
    }
</style>
@endif 