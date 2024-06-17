<?php

namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Illuminate\Support\Facades\Log;


class SmartContractService
{
    private static function getContract()
    {
        $web3 = new Web3('http://172.17.0.1:8545');
        $path = __DIR__.'/ContractABI.json';
        $abi = json_decode(file_get_contents($path), true);

        return new Contract($web3->provider, $abi);
    }

    public static function createAuction($sellerAddress)
    {
        $lastAuctionId = null;

        try {
            $contractAddress = \App\Models\Contract::first()->address;
            $contract = self::getContract();
            $fromAddress = $sellerAddress;
        
            $contract->at($contractAddress)->send('createAuction', 
                [
                    'from' => $fromAddress, 
                    'gas' => 3000000
                ], 
                function($err, $result) use($contract) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        var_dump($result);
                    }
                }
            );

            $contract->at($contractAddress)->call('getLastAuctionIdBySeller', $fromAddress, 
                function($err, $result) use(&$lastAuctionId) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        $lastAuctionId = $result[0];
                    }
                }
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response('Caught exception: '.  $e->getMessage(), 500);
        }

        return $lastAuctionId;
    }

    public static function bid($userAddress, $value, $auctionId)
    {
        try {
            $contractAddress = \App\Models\Contract::first()->address;
            $contract = self::getContract();
            $fromAddress = $userAddress;
        
            $contract->at($contractAddress)->send('bid', $auctionId,
                [
                    'from' => $fromAddress, 
                    'value' => '0x' . \Web3\Utils::toHex(\Web3\Utils::toWei(strval($value), 'ether')),
                    'gas' => 3000000, 
                ], 
                function($err, $result) use($contract) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        var_dump($result);
                    }
                }
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response('Caught exception: '.  $e->getMessage(), 500);
        }
        
        return true;
    }

    public static function cancelAuction($userAddress, $auctionId)
    {
        try {
            $contractAddress = \App\Models\Contract::first()->address;
            $contract = self::getContract();
            $fromAddress = $userAddress;
        
            $contract->at($contractAddress)->send('cancelAuction', $auctionId,
                [
                    'from' => $fromAddress, 
                    'gas' => 3000000, 
                ], 
                function($err, $result) use($contract) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        var_dump($result);
                    }
                }
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response('Caught exception: '.  $e->getMessage(), 500);
        }
        
        return true;
    }

    public static function finishAuction($userAddress, $auctionId)
    {
        try {
            $contractAddress = \App\Models\Contract::first()->address;
            $contract = self::getContract();
            $fromAddress = $userAddress;
        
            $contract->at($contractAddress)->send('finishAuction', $auctionId,
                [
                    'from' => $fromAddress, 
                    'gas' => 3000000, 
                ], 
                function($err, $result) use($contract) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        var_dump($result);
                    }
                }
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response('Caught exception: '.  $e->getMessage(), 500);
        }
        
        return true;
    }

    public static function claimPrize($userAddress, $auctionId)
    {
        try {
            $contractAddress = \App\Models\Contract::first()->address;
            $contract = self::getContract();
            $fromAddress = $userAddress;
        
            $contract->at($contractAddress)->send('claimPrize', $auctionId,
                [
                    'from' => $fromAddress, 
                    'gas' => 3000000, 
                ], 
                function($err, $result) use($contract) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        var_dump($result);
                    }
                }
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response('Caught exception: '.  $e->getMessage(), 500);
        }
        
        return true;
    }

    public static function withdrawCommission($userAddress)
    {
        try {
            $contractAddress = \App\Models\Contract::first()->address;
            $contract = self::getContract();
            $fromAddress = $userAddress;
        
            $contract->at($contractAddress)->send('withdrawCommission',
                [
                    'from' => $fromAddress, 
                    'gas' => 3000000,
                ], 
                function($err, $result) use($contract) {
                    if ($err !== null) {
                        throw $err;
                    }
                    if ($result) {
                        var_dump($result);
                    }
                }
            );
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response('Caught exception: '.  $e->getMessage(), 500);
        }
        
        return true;
    }
}