@extends('layouts.app')

@section('content')
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
    </tr>
    </thead>
    <tbody id="users-list">
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>
                <img src="@if ($user->photo) {{url('storage/' . $user->photo)}} @else {{url('storage/default.jpg')}} @endif" alt="" title="" />
            </td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if ($users->hasMorePages())
    <button class="btn btn-primary" id="load-more" data-page="1">Показать больше</button>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#load-more').click(function() {
            var self = this;
            var nextPage = Number($(this).data('page') + 1);
            var url = location.href + "/?page=" + nextPage;

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $('#users-list').append(data.html);
                    $(self).data('page', nextPage);
                    if (!data.hasMorePages) {
                        $('#load-more').remove();
                    }
                }
            });
        });
    });
</script>
@endsection
