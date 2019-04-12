<div class="card text-white bg-shark border-round">
    @include('partials.machineCard.cardHeader')
    <div class="card-body">
        <div class="card-grid">
            @include('partials.machineCard.nettGross')
            @include('partials.machineCard.scrap')
            @include('partials.machineCard.actualGross')
            @include('partials.machineCard.temperature')
            @include('partials.machineCard.downtime')
            @include('partials.machineCard.oee')
            @include('partials.machineCard.grossChart')
        </div>
    </div>
</div>
