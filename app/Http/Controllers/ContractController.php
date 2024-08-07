<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Auction;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Services\SmartContractService;

class ContractController extends Controller
{
    /**
     * Display the contract.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $contract = Contract::first();

        return Inertia::render('Admin/Contract/Index', ['contract' => $contract]);
    }

    /**
     * Publish the contract for the specified auction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Auction $auction
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function publishContract(Request $request, Auction $auction): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        Gate::authorize('publishContract', $auction);

        $validated = $request->validate([
            'address' => 'required|size:42',
        ]);

        $user = User::find($auction->seller_id);
        if (!$user->isAddressCorrect($validated['address'])) {
            return response('Wrong address.', 500);
        }
        
        try {
            $lastAuctionId = SmartContractService::createAuction($validated['address']);
            $auction->contract_id = $lastAuctionId === 0 ? null : $lastAuctionId;
            $auction->save();
            $user->contract_address = Hash::make($validated['address']);
            $user->save();
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

        return redirect(route('auctions.index'));
    }

    /**
     * Withdraw platform's commission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function withdrawComission(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        Gate::authorize('withdrawComission', Auction::class);

        $validated = $request->validate([
            'address' => 'required|size:42',
        ]);
        
        try {
            SmartContractService::withdrawCommission($validated['address']);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

        return redirect(route('auctions.index'));
    }

    /**
     * Update the contract address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAddress(Request $request): \Illuminate\Http\Response
    {
        $validated = $request->validate([
            'address' => 'required|size:42',
        ]);

        $contract = Contract::first();
        $contract->address = $validated['address'];
        $contract->save();

        return response(null, 200);
    }
}
