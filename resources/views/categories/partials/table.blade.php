<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th>Category Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td class="category-btn">
                    <button type="button" class="btn btn-sm btn-warning edit-btn"
                        data-id="{{ $category->id }}"
                        data-name="{{ $category->name }}"
                        >
                        Edit
                    </button>

                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline-block;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No Category found.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- <div class="d-flex justify-content-center mt-3">
    @if ($products->hasPages())
        <nav>
            <ul class="pagination">
                Prev Button
                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $products->previousPageUrl() }}" tabindex="-1">
                        ⬅️ Prev
                    </a>
                </li>

                Page Numbers
                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                Next Button
                <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $products->nextPageUrl() }}">
                        Next ➡️
                    </a>
                </li>
            </ul>
        </nav>
    @endif
</div> --}}