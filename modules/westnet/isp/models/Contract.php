<?php

namespace app\modules\westnet\isp\models;

use app\modules\config\models\Config;
use app\modules\sale\modules\contract\models\Contract as ContractArya;
use app\modules\westnet\models\Connection;
use yii\base\Model;

/**
 * Description of Contract
 *
 * @author cgarcia
 */
class Contract extends Model {

    public $dhcp_active;
    public $mac_address;
    public $not_invoice_when_is_disabled;
    public $ceil_dfl_percent;
    public $dhcp_mac_address;
    public $node;
    public $pppoe_password;
    public $proxy_arp_lan_gateway;
    public $start_date;
    public $address;
    public $consumption_down_prio;
    public $transaction_kind_id;
    public $ip;
    public $proxy_arp_gateway;
    public $plan_id;
    public $proxy_arp_use_lan_gateway;
    public $invoicing_bank_name;
    public $cablemodem_mac_address;
    public $invoicing_enabled;
    public $electronic_invoicing_enabled;
    public $udp_prio_ports;
    public $transparent_proxy;
    public $prio_protos;
    public $invoicing_frecuency;
    public $traffic_accounting_bandwidth_rate_downgrade;
    public $acl_behaviour;
    public $queue_down_prio;
    public $show_owed_amount_on_invoice_printing;
    public $netmask;
    public $proxy_arp;
    public $prio_helpers;
    public $taxpayer_identification_number;
    public $auto_invoice_generation;
    public $pppoe_username;
    public $charge_in_advance;
    public $send_invoicing_email_when_issue_invoices;
    public $queue_down_dfl;
    public $einvoicing_vat_condition;
    public $consumption_up_prio;
    public $invoicing_generate_reconnection_charge;
    public $automatic_payment_enabled;
    public $id;
    public $proxy_arp_interface_id;
    public $cpe;
    public $client_id;
    public $consumption_up_dfl;
    public $kind_invoice_id;
    public $updated_at;
    public $cablemodem_ip;
    public $pppoe_active;
    public $use_information_contract_for_invoice;
    public $detail;
    public $external_id;
    public $state;
    public $invoicing_bank_account;
    public $date_start;
    public $dhcp_use_mac_control;
    public $start_invoicing_date;
    public $proxy_arp_provider_id;
    public $consumption_down_dfl;
    public $time_modifiers_bandwidth_rate_downgrade;
    public $unique_provider_id;
    public $tcp_prio_ports;
    public $auto_invoice_issuing;
    public $queue_up_dfl;
    public $automatic_state;
    public $is_connected;
    public $corporate_name;
    public $public_address_id;
    public $created_at;
    public $queue_up_prio;
    public $client_id_original;


    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'dhcp_active', 'mac_address', 'not_invoice_when_is_disabled', 'ceil_dfl_percent', 'dhcp_mac_address',
                    'node', 'pppoe_password', 'proxy_arp_lan_gateway', 'start_date', 'address', 'consumption_down_prio',
                    'transaction_kind_id', 'ip', 'proxy_arp_gateway', 'plan_id', 'proxy_arp_use_lan_gateway',
                    'invoicing_bank_name', 'cablemodem_mac_address', 'invoicing_enabled', 'electronic_invoicing_enabled',
                    'udp_prio_ports', 'transparent_proxy', 'prio_protos', 'invoicing_frecuency',
                    'traffic_accounting_bandwidth_rate_downgrade', 'acl_behaviour', 'queue_down_prio',
                    'show_owed_amount_on_invoice_printing', 'netmask', 'proxy_arp', 'prio_helpers',
                    'taxpayer_identification_number', 'auto_invoice_generation', 'pppoe_username', 'charge_in_advance',
                    'send_invoicing_email_when_issue_invoices', 'queue_down_dfl', 'einvoicing_vat_condition',
                    'consumption_up_prio', 'invoicing_generate_reconnection_charge', 'automatic_payment_enabled', 'id',
                    'proxy_arp_interface_id', 'cpe', 'client_id', 'consumption_up_dfl', 'kind_invoice_id', 'updated_at',
                    'cablemodem_ip', 'pppoe_active', 'use_information_contract_for_invoice', 'detail', 'external_id',
                    'state', 'invoicing_bank_account', 'date_start', 'dhcp_use_mac_control', 'start_invoicing_date',
                    'proxy_arp_provider_id', 'consumption_down_dfl', 'time_modifiers_bandwidth_rate_downgrade',
                    'unique_provider_id', 'tcp_prio_ports', 'auto_invoice_issuing', 'queue_up_dfl', 'automatic_state',
                    'is_connected', 'corporate_name', 'public_address_id', 'created_at', 'queue_up_prio', 'client_id_original'
                ], 'safe'
            ],
            [['plan_id', 'client_id'], 'required'],
        ];
    }

    public function __construct($contract, Connection $connection = null, $plan_id = null)
    {
        if(is_array($contract)) {
            $this->load(['Contract'=>$contract]);
        } else {
            $this->external_id      = $contract->contract_id;
            $this->id               = $contract->external_id;
            $this->plan_id          = $plan_id;
            $this->client_id        = $contract->customer_id;
            $this->client_id_original = $contract->customer_id;
            $this->ip               = $connection->getIp41Formatted();
            $this->ceil_dfl_percent = Config::getValue('default_ceil_dfl_percent');
            $this->state            = $connection->status;
            $this->node             = $connection->node->name;
           // $this->created_at       = $contract->from_date;
        }
    }
/*
    public function toArray()
    {
        $values = [
            'external_id' => $this->external_id,
            'plan_id' => $this->plan_id,
            'client_id' => $this->client_id,
            'ceil_dfl_percent' => $this->ceil_dfl_percent,
            'state' => $this->state,
            'node' => $this->node,
            'id' => $this->id
        ];
        if($this->ip != null) {
            $values['ip'] = $this->ip;
        }
        return $values;
    }*/

    public function merge(Contract $contract)
    {
        $this->id = $contract->id;
        $this->external_id = $contract->external_id;
        $this->plan_id = ($contract->plan_id ? $contract->plan_id : $this->plan_id);
        $this->client_id = ($contract->client_id ? $contract->client_id : $this->client_id);
        $this->ceil_dfl_percent = ($contract->ceil_dfl_percent ? $contract->ceil_dfl_percent : $this->ceil_dfl_percent);
        $this->state = ($contract->state ? $contract->state : $this->state);
        $this->node = ($contract->node ? $contract->node : $this->node);;
        //$this->created_at = $contract->created_at;
        $this->ip = ($this->ip==$contract->ip ? null : $contract->ip);
    }
}
