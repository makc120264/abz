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
