<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_order_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('quotes_model');
    }

    /*==============================Chin local updated====================================*/
    public function addSaleOrder($data, $products)
    {
		//$this->erp->print_arrays($data, $products);
		if(isset($data) AND !empty($data) and isset($products) AND !empty($products)){
			$this->db->insert('sale_order',$data);
			$sale_order_id = $this->db->insert_id();
			
			if ($this->site->getReference('sao',$data['biller_id']) == $data['reference_no']) {
				$this->site->updateReference('sao',$data['biller_id']);
			}
			
			if ($data['id']) {
				$this->db->update('quotes', array('issue_invoice' => 'completed'), array('id' => $data['id']));
			}

			if($sale_order_id>0){
				$status = false;
				foreach($products as $product){
					$prod = array(
						'sale_order_id' => $sale_order_id,
						'product_id' => $product['product_id'],
						'product_code' => $product['product_code'],
						'product_name' => $product['product_name'],
						'product_type' => $product['product_type'],
						'option_id' => $product['option_id'],
						'net_unit_price' => $product['net_unit_price'],
						'unit_price' => $product['unit_price'],
						'quantity' => $product['quantity'],
						'warehouse_id' => $product['warehouse_id'],
						'item_tax' => $product['item_tax'],
						'group_price_id'=>$product['group_price_id'],
						'tax_rate_id' => $product['tax_rate_id'],
						'tax' => $product['tax'],
						'discount' => $product['discount'],
						'item_discount' => $product['item_discount'],
						'subtotal' => $product['subtotal'],
						'serial_no' => $product['serial_no'],
						'real_unit_price' => $product['real_unit_price'],
						'product_noted' => $product['product_noted']
						
					);
					
					if($this->db->insert('sale_order_items',$prod)){
						$insert_id = $this->db->insert_id();
					}
				}
				if($insert_id == true){
					return $sale_order_id;
				}
				
			}
			return false;
		
		}
	}
	/*==================================end local updated===============================*/
	
	public function add_deposit($deposit){
		$this->db->insert('deposits',$deposit); 
		if($this->db->affected_rows()>0){
			return true;
		}
		return false; 
	}
	
    public function getInvoiceByIDs($id=null,$wh=null)
    {
        $this->db->select("sale_order.id, sale_order.date, sale_order.reference_no, sale_order.biller, companies.name AS customer, users.username AS saleman,delivery.name as delivery_man,grand_total, paid,(grand_total-paid) as balance")
				->from('sale_order')
				->join('companies', 'companies.id = sale_order.customer_id', 'left')
				->join('users', 'users.id = sale_order.saleman_by', 'left')
				->join('companies as delivery', 'delivery.id = sale_order.delivery_by', 'left')
				->join('deliveries', 'deliveries.sale_id = sale_order.id', 'left')		
                ->where('sale_order.opening_ar!=','2')
				->where("sale_order.id",$id)
				->group_by('sale_order.id');
				if($wh){
					$this->db->where_in('erp_sale_order.warehouse_id',$wh);
				}
				
				$q = $this->db->get();
         if ($q) {
           return $q->row();
        }
        return FALSE;
    }
	 public function getInvoice()
    {
		/*$this->db->select("sales.*, companies.name AS customer, users.username,,delivery.name as delivery_man,(grand_total-paid) as balance")
				->from('sales')
				->join('users', 'users.id = sales.saleman_by', 'left')
				->join('companies', 'companies.id = sales.customer_id', 'left')
				->join('deliveries', 'deliveries.sale_id = sales.id', 'left')
				->join('companies as delivery', 'delivery.id = sales.delivery_by', 'left')
                //->where('sales.opening_ar!=','2')
				->group_by('sales.id');
				//->where(array('sales.id' => $id));
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }*/
		$this->db->select("sale_order.id, sale_order.date, sale_order.reference_no, sale_order.biller, companies.name AS customer, users.username AS saleman,delivery.name as delivery_man,grand_total, paid,(grand_total-paid) as balance")
				 ->from('sale_order')
				 ->join('companies', 'companies.id = sale_order.customer_id', 'left')
				 ->join('users', 'users.id = sale_order.saleman_by', 'left')
				 ->join('companies as delivery', 'delivery.id = sale_order.delivery_by', 'left')
				 ->join('deliveries', 'deliveries.sale_id = sale_order.id', 'left')
				
                //->where('sale_order.opening_ar!=','2')
				//->where("sale_order.id",$id)
				 ->group_by('sale_order.id');
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->result();
		}
		return FALSE;
	}
	
	public function deleteSaleOrderByID($sale_order_id = null){
		
		if($sale_order_id){
			$this->db->delete('erp_sale_order', array('id' => $sale_order_id));
			$this->db->delete('erp_sale_order_items', array('sale_order_id' => $sale_order_id));				
		}
		return false;
	}
	
	public function getSaleOrder($sale_order_id=null){
		$q = $this->db->get_where('erp_sale_order',array('id'=>$sale_order_id));
		if($q->num_rows()>0){
			return $q->row();
		}
		return null;
	}
	public function getCompanyByID($id){
		$this->db->select("erp_companies.*");
		$this->db->join("erp_companies","erp_companies.id = erp_sale_order.customer_id","left");
		$this->db->where('erp_sale_order.id', $id);
		$q = $this->db->get('erp_sale_order');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
	}
	public function getCustomersByArea($area){		
		$this->db->select('id as id, CONCAT(name ," (",company, ")" ) as text');
		$q = $this->db->get_where('companies', array('group_name' => 'customer','group_areas_id' => $area));
        if($q->num_rows() > 0) {
			return $q->result();
		}
		return false;
	}
	public function getSaleOrderItems($sale_order_id=null){
		$q = $this->db->get_where('erp_sale_order_items',array('sale_order_id'=>$sale_order_id));
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$data[] = $row;
			}
			return $data;
		}
		return null;
	}
	
	/*==================================chin local add==============================*/
	public function getAuthorizeSaleOrder($id) {
		if($id) {
			$this->db->update('sale_order', array('order_status' => 'completed'), array('id' => $id));
			return true;
		}
		return false;
	}
	public function getunapproved($id) {
		if($id) {
			$this->db->update('sale_order', array('order_status' => 'pending'), array('id' => $id));
			return true;
		}
		return false;
	}
	public function getrejected($id) {
		if($id) {
			$this->db->update('sale_order', array('order_status' => 'rejected'), array('id' => $id));
			return true;
		}
		return false;
	}
	
	public function getProductByID($id = NULL, $warehouse_id = NULL) {
        $this->db->select('products.*, units.name as unit, products.unit as unit_id, warehouses_products.quantity as wh_qty');
        $this->db->join('units', 'units.id = products.unit', 'left');
		$this->db->join('warehouses_products', 'products.id = warehouses_products.product_id', 'left');
        $q = $this->db->get_where('products', array('products.id' => $id, 'warehouses_products.warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	/*==================================end local add==============================*/
	
	public function getInvoiceByID($id)
    {
		$this->db->select("sale_order.*, companies.name AS customer, users.username AS saleman,delivery.name as delivery_man, sale_order.grand_total, paid,(erp_sale_order.grand_total-paid) as balance, quotes.reference_no AS quotation_no, CASE erp_sale_order.order_status
			WHEN 'completed' THEN
				'Approved'
			WHEN 'rejected' THEN
				'Rejected'
			WHEN 'pending' THEN
				'Order'
			END AS status,COALESCE (SUM(erp_deposits.amount), 0) AS deposit,
			erp_sale_order.grand_total - COALESCE (SUM(erp_deposits.amount), 0) AS balance")
				 ->join('companies', 'companies.id = sale_order.customer_id', 'left')
				 ->join('users', 'users.id = sale_order.saleman_by', 'left')
				 ->join('companies as delivery', 'delivery.id = sale_order.delivery_by', 'left')
				 ->join('deliveries', 'deliveries.sale_id = sale_order.id', 'left')
				 ->join('quotes', 'sale_order.quote_id = quotes.id', 'left')
				 ->join('deposits', 'erp_deposits.so_id = erp_sale_order.id', 'left')
				 ->group_by('sale_order.id');
		$q = $this->db->get_where('sale_order', array('sale_order.id' => $id));
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return FALSE;
		
    }
	
	public function getAllInvoiceItems($sale_id)
    {
        $this->db->select('sale_order_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, (CASE WHEN erp_products.unit = 0 THEN erp_products.unit ELSE erp_units.name END) as uname, products.details as details, product_variants.name as variant, products.unit, products.promotion, categories.name AS category_name')
            ->join('products', 'products.id=sale_order_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_order_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_order_items.tax_rate_id', 'left')
			->join('categories', 'categories.id = products.category_id', 'left')
            ->join('units', 'units.id = products.unit', 'left')
            ->group_by('sale_order_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('sale_order_items', array('sale_order_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
	
	public function getAllInvoiceItemsById($sale_id){
		
		$this->db->select('sale_order_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, (CASE WHEN erp_products.unit = 0 THEN erp_products.unit ELSE erp_units.name END) as uname, products.details as details, product_variants.name as variant, products.unit, products.promotion, categories.name AS category_name')
            ->join('products', 'products.id=sale_order_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_order_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=sale_order_items.tax_rate_id', 'left')
			->join('categories', 'categories.id = products.category_id', 'left')
            ->join('units', 'units.id = products.unit', 'left')
            ->group_by('sale_order_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('sale_order_items', array('sale_order_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
		
	}
	
	public function getSaleOrderItemsDetail($sale_order_id = NULL){
		$this->db
		->select('erp_sale_order_items.*,erp_product_variants.name as package_name, units.name as unit')
		->where('erp_sale_order_items.sale_order_id',$sale_order_id)
		->join('products', 'products.id = sale_order_items.product_id', 'left')
		->join('erp_product_variants','erp_sale_order_items.option_id = erp_product_variants.id','left')
		->join('units', 'units.id = products.unit', 'left')
		->from('erp_sale_order_items');
		$q = $this->db->get();
		if($q->num_rows()>0){
			return $q->result();
		}
		return false;
	}

	public function getDeliveriesInvoiceByID($id)
    {
    	$this->db->select('deliveries.*, delivery_items.warehouse_id, users.username as saleman')
    			 ->join('delivery_items', 'deliveries.id = delivery_items.delivery_id', 'left')
    			 ->join('users', 'deliveries.created_by = users.id', 'left');
        $q = $this->db->get_where('deliveries', array('deliveries.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllDeliveryInvoiceItems($delivery_id)
    {
        $this->db->select('erp_products.`code`,erp_deliveries.*, erp_delivery_items.product_name as description, delivery_items.quantity_received as qty, erp_companies.name, erp_units.name as unit, delivery_items.category_name as brand, delivery_items.option_id, product_variants.name as variant, (erp_delivery_items.quantity_received * erp_product_variants.qty_unit) as variant_qty');
		$this->db->from('deliveries');
		$this->db->join('erp_companies','deliveries.delivery_by = erp_companies.id','left');
		$this->db->join('delivery_items','deliveries.id = delivery_items.delivery_id', 'left');
		$this->db->join('product_variants','delivery_items.option_id = product_variants.id', 'left');
		$this->db->join('erp_products','delivery_items.product_id = erp_products.id', 'left');
		$this->db->join('erp_units','erp_products.unit = erp_units.id', 'left');
		$this->db->group_by('delivery_items.id');
		
		$this->db->where('erp_deliveries.id',$delivery_id);
		$q = $this->db->get();
		if($q->num_rows()>0){
			foreach($q->result() as $result){
				$data[] = $result;
			}
			return $data;
		}
		return NULL;
		
    }
	public function getVar($id){
        $q = $this->db->get_where('erp_product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
	public function assign_to_user($user_id=NULL,$so_id=NULL)
	{

		if($this->db->update('sale_order', array('assign_to_id' => $user_id), array('reference_no' => $so_id))){
			return true;
		}
		return false;
	}

	public function getAllCompaniesByID($biller_id) {
        $this->db->select('companies.*')
                 ->from('companies')
                 ->where_in("id", $biller_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
	function getExchange_rate($code = "KHM")
    {	
		$this->db->where(array('code' => $code));
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
	
}
