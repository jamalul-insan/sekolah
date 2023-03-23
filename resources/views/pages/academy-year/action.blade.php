<div class="btn btn-group btn-sm">
    <a href="{{ route('academic-years.edit', ['academic_year' => $row->id]) }}" class="btn btn-info btn-icon-split">
        <span class="icon text-white-50">
            <i class="fas fa-info-circle"></i>
        </span>
        <span class="text">EDIT</span>

    </a>

    <button data-url="{{ route('academic-years.edit', ['academic_year' => $row->id]) }}" onclick="confirm('yes to delete')"
        class="btn btn-danger btn-icon-split">
        <span class="icon text-white-50">
            <i class="fas fa-trash"></i>
        </span>
        <span class="text">DELETE</span>
    </button>
</div>
