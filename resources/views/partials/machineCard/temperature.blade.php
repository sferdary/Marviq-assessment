<div class="temperature data-col">
    <h4>Temperature</h4>
    <table class="table table-sm table-borderless">
        <tr>
            <td><strong>Highest</strong></td>
            <td class="text-danger"><strong>{{$machine['temperature']['maxTemp']}}</strong></td>
            <td>°C</td>
        </tr>
        <tr>
            <td><strong>Lowest</strong></td>
            <td class="text-success"><strong>{{$machine['temperature']['minTemp']}}</strong></td>
            <td>°C</td>
        </tr>
        <tr>
            <td><strong>Average</strong></td>
            <td><strong>{{$machine['temperature']['avgTemp']}}</strong></td>
            <td>°C</td>
        </tr>
    </table>
</div>
