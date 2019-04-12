<div class="oee data-col">
    <h4>OEE</h4>
    <div class="circle-diagram">
        <svg viewBox="0 0 36 36">
            <path d="
				M18 2.0845
      			a 15.9155 15.9155 0 0 1 0 31.831
      			a 15.9155 15.9155 0 0 1 0 -31.831"
                fill="none"
                stroke="##28a745"
                stroke-width="3"
                stroke-dasharray=" 100, 100">
            </path>
            <path d="
				M18 2.0845
      			a 15.9155 15.9155 0 0 1 0 31.831
      			a 15.9155 15.9155 0 0 1 0 -31.831"
                fill="none"
                stroke="#28a745"
                stroke-width="3"
                stroke-dasharray=" {{$machine['oee']}}, 100">
            </path>
            <text class="percentage" x="50%" y="55%" text-anchor="middle" fill="#ffffff">
                {{$machine['oee']}}%
            </text>
        </svg>
    </div>
</div>
