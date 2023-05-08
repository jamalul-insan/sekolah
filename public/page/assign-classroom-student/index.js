

    const tableStudents = $("#students")

    const tableClassroomStudents = $("#students-classroom")

    const btnStore = $("#assign-classroom-store")

    const btnDestroy = $("#assign-classroom-delete")

    const classroomId = $("#classroom_id")

    const setClassroomId = $("input[name='classroom_id']")

    const academy_year = $("#academy_year")

    const session_classroom = $("#session_classroom")

    const columns = [
        {"data": "id"},
        {"data": "nis"},
        {"data": "name"},
        {"data": "dob"}
    ];



    var selectedStudentStore = new Set()
    var selectedStudentDelete = new Set()
    var classroomSetData = {}

    var getClassroom = function(id){
        let currentClassroom = session_classroom.val();
        var result = null;
        $.get(route('get-classroom')+'?academy_year_id='+id,function(data){
            classroomId.empty();
            classroomId.append('<option value="">Kelas</option>');
            for (let i = 0; i < data.classrooms.length; i++) {
                let isSelected = currentClassroom == data.classrooms[i].id ? 'selected="true"' : '';

                classroomId.append(`
                <option value="${data.classrooms[i].id}" ${isSelected}>
                 ${data.classrooms[i].grade.grade_name} -
                 ${data.classrooms[i].name}</option>
                `);
            }
            result = data;
        });
        return result;
    }

    getClassroom(academy_year.val());




    var hideBtn = (id,data) =>{data.size >= 1 ? id.show() : id.hide() };

    var selectedStore = (e) => {
        let isSelected = $(e).is(":checked");
        let valSelected = parseInt($(e).val());
        if (isSelected) {
            $(e).parent().parent().addClass("selected")
            selectedStudentStore.add(valSelected)
        } else {
            $(e).parent().parent().removeClass("selected")
            selectedStudentStore.delete(valSelected)
        }
        hideBtn(btnStore,selectedStudentStore)
    }

    var selectedDestroy = (e) => {
        let isSelected = $(e).is(":checked");
        let valSelected = parseInt($(e).val());
        if (isSelected) {
            $(e).parent().parent().addClass("selected")
            selectedStudentDelete.add(valSelected)
        } else {
            $(e).parent().parent().removeClass("selected")
            selectedStudentDelete.delete(valSelected)
        }
        hideBtn(btnDestroy,selectedStudentDelete)
    }

    var reloadajax = (e) => {
        setDataClassroomID()
        e.ajax.reload(null, false)
    }




    var setDataClassroomID = ()=>{

        let classroom_id = classroomId.find(":selected").val()

        if(classroom_id.trim() == 0  ){
            classroom_id = session_classroom.val()

        }
        setClassroomId.val(classroom_id)
        classroomSetData.classroom_id = classroom_id

    }

    var datatableStudent = (table,url,dataSelected,selectedRowFun)  =>{
        setDataClassroomID()
       return table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                data: function(d) {
                    return $.extend(d, classroomSetData)
                },
                url: url
            },
            columns: columns,
            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                'render': function(data, type, full, meta) {

                    let isChecked = dataSelected.has(data) ? 'checked' : '';
                    return `<input type="checkbox" name="id[]" onclick="${selectedRowFun}(this)" value="${data}" ${isChecked} />`
                }
            }],
            'order': [2, 'asc'],
            createdRow: function(row, data, dataIndex) {
                if (dataSelected.has(data.id)) {
                    $(row).addClass('selected');
                } else {
                    $(row).removeClass('selected');
                }
            }
        });
    }


    var appenDataStudent = (data,btn) => {
        for (var it = data.values(), val = null; val = it.next().value;) {
            btn.append(`<input type="hidden" name="id[]" value="${val}" /> `)
        }
    }

    var datatableStudents = datatableStudent(
        tableStudents,
        route('datatable.assign-students'),
        selectedStudentStore,
        'selectedStore'
        )

    var datatableStudentRoom = datatableStudent(
                tableClassroomStudents,
                route('datatable.assign-classroom-student'),
                selectedStudentDelete,
                'selectedDestroy'
            )




    hideBtn(btnStore,selectedStudentStore)
    hideBtn(btnDestroy,selectedStudentDelete)

    btnStore.click(()=>{
        appenDataStudent(selectedStudentStore,btnStore)
    });

    btnDestroy.click(()=>{
        appenDataStudent(selectedStudentDelete,btnDestroy)
    });


    academy_year.change(function(){
        getClassroom($(this).val())
        session_classroom.val('')
    })
    classroomId.change(function() {
        reloadajax(datatableStudentRoom)
    });
