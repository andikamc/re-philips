@extends('layouts.app')

@section('header')
<div class="page-head">
    <!-- BEGIN PAGE TITLE -->
    <div class="page-title">
        <h1>Feedback Answers
            <small>manage feedback Answer</small>
        </h1>
    </div>
    <!-- END PAGE TITLE -->
</div>
<ul class="page-breadcrumb breadcrumb">
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span class="active">Feedback Answer Management</span>
    </li>
</ul>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12 col-lg-3 col-md-3 col-sm-6 col-xs-12">

        <!-- BEGIN FILTER-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-map-o font-blue"></i>
                        <span class="caption-subject font-blue bold uppercase">FILTER FEEDBACK</span>
                    </div>
                </div>

                <div class="caption padding-caption">
                    <span class="caption-subject font-dark bold uppercase" style="font-size: 12px;"><i class="fa fa-cog"></i> BY DETAILS</span>
                </div>
                
                <div class="row filter" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <select id="filterAssessor" class="select2select">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterPromoter" class="select2select">
                            <option value=""></option>
                        </select>
                    </div>
                </div>

                <br>

                <div class="btn-group">
                    <a href="javascript:;" class="btn red-pink" id="resetButton" onclick="triggerReset(paramReset)">
                        <i class="fa fa-refresh"></i> Reset </a>
                    <a href="javascript:;" class="btn blue-hoki"  id="filterButton" onclick="filteringReport(paramFilter)">
                        <i class="fa fa-filter"></i> Filter </a>
                </div>

                <br><br>

            </div>
        <!-- END FILTER-->

        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-map-o font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">Feedback Answer</span>
                </div>
            </div>
            <div class="portlet-body" style="padding: 15px;">
                <!-- MAIN CONTENT -->

                <div class="row">

<!--                     <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group">
                                    <a id="add-feedbackAnswer" class="btn green" data-toggle="modal" href="#feedbackAnswer"><i
                                        class="fa fa-plus"></i> Add Feedback Answer </a>

                                </div>
                            </div>
                        </div>
                    </div> -->

                    <table class="table table-striped table-hover table-bordered" id="feedbackAnswerTable" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th> No. </th>
                                <th> Assessor </th>
                                <th> Promoter </th>
                                <th> Question </th>   
                                <th> Score </th>    
                                <th> Options </th>                        
                            </tr>
                        </thead>
                    </table>                 

                </div>

                @include('partial.modal.feedbackAnswer-modal')

                <!-- END MAIN CONTENT -->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>

@endsection


@section('additional-scripts')

<!-- BEGIN SELECT2 SCRIPTS -->
<script src="{{ asset('js/handler/select2-handler.js') }}" type="text/javascript"></script>
<!-- END SELECT2 SCRIPTS -->
<!-- BEGIN RELATION SCRIPTS -->
<script src="{{ asset('js/handler/relation-handler.js') }}" type="text/javascript"></script>
<!-- END RELATION SCRIPTS -->
<!-- BEGIN PAGE VALIDATION SCRIPTS -->
<script src="{{ asset('js/handler/feedbackAnswer-handler.js') }}" type="text/javascript"></script>
<!-- END PAGE VALIDATION SCRIPTS -->

<script>
    /*
     *
     *
     */

        var filterId = ['#filterAssessor', '#filterPromoter'];
        var url = 'datatable/feedbackAnswer';
        var order = [ [0, 'desc'] ];
        var columnDefs = [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [2,4,5]},
            ];

        var tableColumns = [
                {data: 'id', name: 'id'},
                {data: 'assessor_name', name: 'assessor_name'},
                {data: 'promoter_name', name: 'promoter_name'},
                {data: 'feedback_question', name: 'feedback_question'},
                {data: 'answer', name: 'answer'},
                {data: 'action', name: 'action', searchable: false, sortable: false},              
            ];
        var paramFilter = ['feedbackAnswerTable', $('#feedbackAnswerTable'), url, tableColumns, columnDefs, order];
        var paramReset = [filterId, 'feedbackAnswerTable', $('#feedbackAnswerTable'), url, tableColumns, columnDefs, order];


    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Set data for Data Table
        var table = $('#feedbackAnswerTable').dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('datatable.feedbackAnswer') }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'assessor_name', name: 'assessor_name'},
                {data: 'promoter_name', name: 'promoter_name'},
                {data: 'feedback_question', name: 'feedback_question'},
                {data: 'answer', name: 'answer'},
                {data: 'action', name: 'action', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0]},
                {"className": "dt-center", "targets": [2,4,5]},
            ],
            "order": [ [0, 'desc'] ],
        });


        // Delete data with sweet alert
        $('#feedbackAnswerTable').on('click', 'tr td button.deleteButton', function () {
            var id = $(this).val();

                // if(feedbackAnswerRelation(id)){
                //     swal("Warning", "This data still related to others! Please check the relation first.", "warning");
                //     return;
                // }

                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it",
                    cancelButtonText: "No, cancel",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })


                        $.ajax({

                            type: "DELETE",
                            url:  'feedbackAnswer/' + id,
                            success: function (data) {
                                $("#"+id).remove();
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });

                        swal("Deleted!", "Data has been deleted.", "success");
                    } else {
                        swal("Cancelled", "Data is safe ", "success");
                    }
                });
        });

        initSelect2();

    });

    // Init add form
    $(document).on("click", "#add-feedbackAnswer", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "ADD NEW ";

        select2Reset($("#assessor"));
        select2Reset($("#promoter"));
        select2Reset($("#question"));
        select2Reset($("#answer"));

        // Set action url form for add
        var postDataUrl = "{{ url('feedbackAnswer') }}";
        $("#form_feedbackAnswer").attr("action", postDataUrl);

        // Delete Patch Method if Exist
        if($('input[name=_method]').length){
            $('input[name=_method]').remove();
        }

    });


    // For editing data
    $(document).on("click", ".edit-feedbackAnswer", function () {

        resetValidation();

        var modalTitle = document.getElementById('title');
        modalTitle.innerHTML = "EDIT";

        var id = $(this).data('id');
        var getDataUrl = "{{ url('feedbackAnswer/edit/') }}";
        var postDataUrl = "{{ url('feedbackAnswer') }}"+"/"+id;

        // Set action url form for update
        $("#form_feedbackAnswer").attr("action", postDataUrl);

        // Set Patch Method
        if(!$('input[name=_method]').length){
            $("#form_feedbackAnswer").append("<input type='hidden' name='_method' value='PATCH'>");
        }
        $.get(getDataUrl + '/' + id, function (data) {
           // console.log(data.answer)
                    setSelect2IfPatchModal($("#assessor"), data.assessor_id, data.assessor.name);
                    setSelect2IfPatchModal($("#promoter"), data.promoter_id, data.promoter.name);
                    setSelect2IfPatchModal($("#question"), data.feedbackQuestion_id, data.feedback_question.question);
                    setSelect2IfPatchModal($("#answer"), data.answer, data.answer);


            })

        });

        function initSelect2(){
            // console.log('ok');
            /*
             * Select 2 init
             *
             */

            $('#assessor').select2(setOptions('{{ route("data.nonPromoter") }}', 'Assessor', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));

            $('#promoter').select2(setOptions('{{ route("data.groupPromoter") }}', 'Promoter', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));

            $('#question').select2(setOptions('{{ route("data.feedbackQuestion") }}', 'Feedback Question', function (params) {
                return filterData('question', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.question}
                    })
                }
            }));

            $('#answer').select2({
                width: '100%',
                placeholder: 'answer'
            })    
            $('#filterAssessor').select2(setOptions('{{ route("data.nonPromoter") }}', 'Assesssor', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterAssessor').on('select2:select', function () {
                self.selected('byAssesssor', $('#filterAssessor').val());
            });

            $('#assessor').on('select2:select', function () {
                self.selected('byAssesssor', $('#assessor').val());
            });

            $('#filterPromoter').select2(setOptions('{{ route("data.groupPromoter") }}', 'Promoter', function (params) {
                return filterData('employee', params.term);
            }, function (data, params) {
                return {
                    results: $.map(data, function (obj) {
                        return {id: obj.id, text: obj.name}
                    })
                }
            }));
            $('#filterPromoter').on('select2:select', function () {
                self.selected('byPromoter', $('#filterPromoter').val());
            });



        }

</script>
@endsection
