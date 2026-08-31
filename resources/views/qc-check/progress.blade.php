@extends('layouts.app')

@section('content')
<div class="container text-center">

    <h4>QC Processing...</h4>

    <div class="progress mt-4" style="height: 30px;">
        <div id="progressBar"
             class="progress-bar progress-bar-striped progress-bar-animated"
             style="width: 0%">
            0%
        </div>
    </div>

    <p class="mt-3" id="statusText">Starting...</p>

    <div id="downloadSection" class="mt-4 d-none">
        <a id="downloadBtn" class="btn btn-success" onclick="startZip()">
            ⬇ Download ZIP
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
const batchId = "{{ $batchId }}";

const interval = setInterval(() => {
    fetch(`/qc/progress/${batchId}`)
        .then(res => res.json())
        .then(data => {

            if (!data) return;

            let percent = Math.round((data.processed / data.total) * 100);
            percent = isNaN(percent) ? 0 : percent;

            const bar = document.getElementById('progressBar');
            bar.style.width = percent + '%';
            bar.innerText = percent + '%';

            document.getElementById('statusText')
                .innerText = `${data.processed} / ${data.total} PDFs processed`;

            if (data.status === 'completed') {
                clearInterval(interval);

                bar.classList.remove('progress-bar-animated');

                document.getElementById('statusText')
                    .innerText = 'QC Completed Successfully';

                document.getElementById('downloadSection')
                    .classList.remove('d-none');

                document.getElementById('downloadBtn')
                    .href = `/qc/download/${data.batch_name ?? ''}`;
            }
        });
}, 1500);



function startZip() {
    const btn = document.getElementById('downloadBtn');
    btn.innerText = 'Preparing ZIP...';
    btn.classList.add('disabled');
}
</script>
@endpush
