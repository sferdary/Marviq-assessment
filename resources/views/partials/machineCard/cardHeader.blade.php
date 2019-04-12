<div class="card-header">
    <h1>Machine {{$machine['number']}}</h1>
    <p>{{$machine['type']}} brick mould</p>
    <span><strong class="text-danger">{{ ($result['2x2']['production']['chartData'] === 0) ? "No data available" : "" }}</strong></span>
</div>
