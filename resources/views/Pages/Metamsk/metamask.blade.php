@extends('Layouts.app')
@section('header')
<div class="header pb-6">
    <div class="header pb-6">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-12 col-12">
                        <h1 class=" row justify-content-center">Example for receiving test Ethereum in 5 min</h1>
                        <h3 class=" row justify-content-center">(You only can receive up to  2 Ether.)</h3>
                        <nav aria-label="breadcrumb" class="d-none d-md-block ">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fas fa-home"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Meta Mask Integration</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('content')
    <div class="row justify-content-center">
        <div class="col-lg-4 text-center">
            <div class="form-group">
                <h3>Enter amount Here</h3>
                <input type="text" class="form-control" name="amount" id="inp_amount" aria-describedby="helpId"
                    placeholder="Enter Amount">
            </div>
            <button type="button" onClick="startProcess()" class="btn btn-success">Receive Now</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="margin-top:100px;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Transactions</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>TxHash</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $key => $transaction)
                            <tr>
                                <td scope="row">{{ $key+1 }}</td>
                                <td>{{ $transaction->txHash }}</td>
                                <td>{{ $transaction->amount }} ETH</td>
                                <td>
                                    @switch($transaction->status)
                                    @case(1)
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                                    @case(2)
                                    <span class="badge badge-success">Success</span>
                                    @break
                                    @case(3)
                                    <span class="badge badge-danger">Declined</span>
                                    @break
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12 mb-4 text-center">
            <h4 class="text-primary"><a target="_blank" href="https://github.com/superstar0891/Intergration wallet">GitHub
                    Repositary</a></h4>
            <h4 class="text-dark">Thank You VeryMuch</h4>
        </div>
    </div>
    @endsection
    @push('js')
    <script>
        function startProcess() {
            if ($('#inp_amount').val() > 0) {
                // run metamsk functions here
                EThAppDeploy.loadEtherium();
            } else {
                alert('Please Enter Valid Amount');
            }
        }


        EThAppDeploy = {
            loadEtherium: async () => {
                if (typeof window.ethereum !== 'undefined') {
                    EThAppDeploy.web3Provider = ethereum;
                    EThAppDeploy.requestAccount(ethereum);
                } else {
                    alert(
                        "Not able to locate an Ethereum connection, please install a Metamask wallet"
                    );
                }
            },
            /****
             * Request A Account
             * **/
            requestAccount: async (ethereum) => {
                ethereum
                    .request({
                        method: 'eth_requestAccounts'
                    })
                    .then((resp) => {
                        //do payments with activated account
                        EThAppDeploy.payNow(ethereum, resp[0]);
                    })
                    .catch((err) => {
                        // Some unexpected error.
                        console.log(err);
                    });
            },
            /***
             *
             * Do Payment
             * */
            payNow: async (ethereum, from) => {
                var amount = $('#inp_amount').val();
                ethereum
                    .request({
                        method: 'eth_sendTransaction',
                        params: [{
                            from: from,
                            to: "0x5bc7e50e78E03607eE339fA4889cFBA8fa2fbddF",
                            value: '0x' + ((amount * 1000000000000000000).toString(16)),
                        }, ],
                    })
                    .then((txHash) => {
                        if (txHash) {
                            console.log(txHash);
                            storeTransaction(txHash, amount);
                        } else {
                            console.log("Something went wrong. Please try again");
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
        }
        /***
         *
         * @param Transaction id
         *
         */
        function storeTransaction(txHash, amount) {
            $.ajax({
                url: "{{ route('metamask.transaction.create') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {
                    txHash: txHash,
                    amount: amount,
                },
                success: function (response) {
                    // reload page after success
                    window.location.reload();
                }
            });
        }

    </script>
    @endpush
