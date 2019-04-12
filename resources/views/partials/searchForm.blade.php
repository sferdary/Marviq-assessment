<h2 class="m-2 text-dark">{{ (isset($_POST['date']) == true) ? (new DateTime($_POST['date']))->format('l d F Y') :  (new DateTime("2018-01-01"))->format('l d F Y') }}</h2>
<form action="{{route('search')}}" method="POST" class="mt-3 ml-2">
    {{ csrf_field() }}
    <div class="form-group">
        <input type="date" class="form-control-sm" value="{{ (isset($_POST['date']) == true) ? $_POST['date'] : '2018-01-01' }}" name="date">
        <input type="submit" value="search" class="btn btn-sm btn-outline-success">
    </div>
</form>
