<?php

namespace App\Domains\Admin\Role\DataTables;

use App\Domains\Core\Role\Models\Role;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;

class RoleDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('created_at', function($record) {
                return $record->created_at->translatedFormat(config('constant.date_format.date'));
            })
            ->editColumn('name', function($record){
                return $record->name ? ucwords($record->name) : '-';
            })
            ->addColumn('action', function ($record) {
                $actionHtml = '<div class="action-col">';
                // View button for all users who have permission

                if ($record) {
                    if (Gate::check('role_show')) {
                        $actionHtml .= view('Components::action-button', [
                            'route' => route('roles.show', $record->id),
                            'class' => 'btnViewRole',
                            'btnType' => 'view',
                        ])->render();
                    }

                    if($record->id != config('constant.roles.super_admin')){
                        if (Gate::check('role_edit')) {
                            $actionHtml .= view('Components::action-button', [
                                'route' => route('roles.edit', $record->id),
                                'class' => 'btnEditRole',
                                'btnType' => 'edit',
                            ])->render();
                        }
                    }
                }
                $actionHtml .= '</div>';
                return $actionHtml;
            })
            ->setRowId('id')
            ->filterColumn('created_at', function ($query, $keyword) {
                $searchDateFormat = config('constant.search_date_format.date_time');
                $query->whereRaw("DATE_FORMAT(created_at,'$searchDateFormat') like ?", ["%$keyword%"]); //date_format when searching using date
            })
            ->rawColumns(['action', 'role_status']);
    }

    public function query(Role $model): QueryBuilder
    {
        return $model->whereNotIn('id', config('constant.roles'))->newQuery();
    }

    public function html(): HtmlBuilder
    {
        $orderByColumn = 1;   
        return $this->builder()
            ->setTableId('role-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy($orderByColumn,'asc') 
            ->selectStyleSingle()
            // ->lengthMenu([ 
            //         [10, 25, 50, 100, /*-1*/],
            //         [10, 25, 50, 100, /*'All'*/]
            //     ])
            ->pageLength(50)
            ->parameters([
                // 'pageLength' => $pagination['pageLength'],
                //'dom' => 'frtip',
                'responsive' => true, // keep responsive enabled
                'pagingType' => 'simple_numbers',
                'language' => [
                    "sZeroRecords" => trans('cruds.datatable.data_not_found'),
                    "sProcessing" => trans('cruds.datatable.processing'),
                    "sLengthMenu" => trans('cruds.datatable.show') . " _MENU_ " . trans('cruds.datatable.entries'),
                    "sInfo" =>  config('app.locale') == 'en' ?
                        trans('cruds.datatable.showing') . " _START_ " . trans('cruds.datatable.to') . " _END_ " . trans('cruds.datatable.of') . " _TOTAL_ " . trans('cruds.datatable.records') :
                        trans('cruds.datatable.showing') . "_TOTAL_" . trans('cruds.datatable.to') . trans('cruds.datatable.of') . "_START_-_END_" . trans('cruds.datatable.records'),
                    "sInfoEmpty" => trans('cruds.datatable.showing') . " 0 " . trans('cruds.datatable.to') . " 0 " . trans('cruds.datatable.of') . " 0 " . trans('cruds.datatable.records'),
                    "search" => trans('cruds.datatable.search'),
                    "sEmptyTable" => trans('cruds.datatable.data_not_found'),
                    "paginate" => [
                        "first" => trans('cruds.datatable.first'),
                        "last" => trans('cruds.datatable.last'),
                        "next" =>  trans('cruds.datatable.next'),
                        "previous" =>  trans('cruds.datatable.previous'),
                    ],
                    "autoFill" => [
                        "cancel" => trans('global.cancel'),
                    ],

                ],
                // 'drawCallback' => 'function(settings) {
                //     var api = this.api();
                //     var data = api.rows({ page: "current" }).data();
 
                //     var hasData = data.length > 0;
                //     var columnCount = $("#role-table").find("th").length;
                //     // Store state globally
                //     $("#role-table").data("has_data", hasData);
                //     $("#role-table").attr("data-has_data", hasData);
 
                //     // If there are no records, disable Responsive child rows
                //     if (!hasData) {
                //        setTimeout(function(){
                //                 $("#role-table").find("th, td").css("display", "table-cell");
                //                 $("#role-table").find(".dt-empty").attr("colspan", columnCount);
                //             }, 500);
                //     }
 
                //     $(window).on("resize", function () {
                //         var hasData = $("#role-table").data("has_data");
 
                //         if (!hasData) {
                //             // Ensure all columns remain visible on resize if no data
                //            setTimeout(function(){
                //                 $("#role-table").find("th, td").css("display", "table-cell");
                //                 $("#role-table").find(".dt-empty").attr("colspan", columnCount);
                //             }, 500);
                //         }
                //     });
 
                //     var rows = data.length;
                //     var pageLength = api.page.len();
                //     var recordsTotal = api.page.info().recordsTotal;
                //     if (recordsTotal > pageLength) {
                //         $(this).closest(".dt-container").find(".dt-paging.paging_simple_numbers").show();
                //     } else {
                //         $(this).closest(".dt-container").find(".dt-paging.paging_simple_numbers").hide();
                //     }
                // }'
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(trans('global.sno'))->orderable(false)->searchable(false)->width(30)->addClass('dt-sno'),
            Column::make('name')->title(trans('cruds.role.fields.name')),
            Column::make('created_at')->title(trans('cruds.role.fields.created_at')),
            Column::computed('action')->orderable(false)->title(trans('global.action'))
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center w-100px'),
        ];
    }

    protected function filename(): string
    {
        return 'Roles' . date('YmdHis');
    }
}
