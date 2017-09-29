<?php
namespace ZfDonate\Model\Adapter;

use ZfDonate\Model\DonationEntity;

interface StorageAdapterInterface {
	public function save(DonationEntity $donationEntity);
	public function fetchWithId($id) : DonationEntity;
	public function fetchWithTransactionId(string $transaction_id) : DonationEntity;
}