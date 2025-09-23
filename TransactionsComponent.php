<?php

namespace Apps\Fintech\Components\Etf\Transactions;

use Apps\Fintech\Packages\Adminltetags\Traits\DynamicTable;
use Apps\Fintech\Packages\Etf\Transactions\EtfTransactions;
use System\Base\BaseComponent;

class TransactionsComponent extends BaseComponent
{
    use DynamicTable;

    protected $etfTransactionsPackage;

    public function initialize()
    {
        $this->etfTransactionsPackage = $this->usePackage(EtfTransactions::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $transaction = $this->etfTransactionsPackage->getById((int) $this->getData()['id']);

                if (!$transaction) {
                    return $this->throwIdNotFound();
                }

                $this->view->transaction = $transaction;
            }

            $this->view->pick('transactions/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'etf/transactions',
                    'remove'    => 'etf/transactions/remove'
                ]
            ];

        $conditions =
            [
                'conditions'    => '-|user_id|equals|' . $this->access->auth->account()['id'] . '&'
            ];

        $this->generateDTContent(
            $this->etfTransactionsPackage,
            'etf/transactions/view',
            $conditions,
            ['date', 'amount', 'type', 'user_id', 'portfolio_id'],
            true,
            ['date', 'amount', 'type', 'user_id', 'portfolio_id'],
            $controlActions,
            null,
            null,
            'date'
        );

        $this->view->pick('transactions/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->etfTransactionsPackage->addEtfTransaction($this->postData());

        $this->addResponse(
            $this->etfTransactionsPackage->packagesData->responseMessage,
            $this->etfTransactionsPackage->packagesData->responseCode,
            $this->etfTransactionsPackage->packagesData->responseData ?? []
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->etfTransactionsPackage->updateEtfTransaction($this->postData());

        $this->addResponse(
            $this->etfTransactionsPackage->packagesData->responseMessage,
            $this->etfTransactionsPackage->packagesData->responseCode,
            $this->etfTransactionsPackage->packagesData->responseData ?? []
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->etfTransactionsPackage->removeEtfTransaction($this->postData());

        $this->addResponse(
            $this->etfTransactionsPackage->packagesData->responseMessage,
            $this->etfTransactionsPackage->packagesData->responseCode,
            $this->etfTransactionsPackage->packagesData->responseData ?? []
        );
    }
}