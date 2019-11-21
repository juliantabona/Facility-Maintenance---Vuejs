
function getFormFields() {
    return {
        name: null,
        description: null,
        type: null,
        cost_per_item: null,
        unit_regular_price: null,
        unit_sale_price: null,
        sku: null,
        barcode: null,
        stock_quantity: null,
        allow_stock_management: null,
        auto_manage_stock: null,
        variant_attributes: null,
        allow_variants: null,
        allow_downloads: null,
        show_on_store: null,
        is_new: null,
        is_featured: null
    }
}

function getFormRules() {
    return {
        name: [
            { required: true, message: 'Enter name' }
        ]
    }
}

function getCustomErrorFields() {
    return getFormFields();
}

function resetCustomErrorFields(self) {
    self.customErrors = getCustomErrorFields();
}

export default {
    getFormRules,
    getFormFields,
    getCustomErrorFields,
    initiateUpdate(self) {
        
        var validation;

        //  Lets validate the current form
        self.$refs['formData'].validate((valid) => {
            //  If the form is not valid
            if(valid){
                //  Set validation to true
                validation = true;
            }else{
                //  Set validation to false
                validation = false;
            }    
        });

        /*  IF THIS FORM IS VALID LETS UPDATE THE USSD INTERFACE INFO */
        if(validation){
            
            //  Reset all our custom server errors
            resetCustomErrorFields(self);

            //  Start loader
            self.isUpdating = true;

            console.log('Attempt to update product using the following...');   

            //  Product data to update
            let updateData = {
                name: self.formData.name,
                description: self.formData.description,
                type: self.formData.type,
                cost_per_item: self.formData.cost_per_item,
                unit_regular_price: self.formData.unit_regular_price,
                unit_sale_price: self.formData.unit_sale_price,
                sku: self.formData.sku,
                barcode: self.formData.barcode,
                stock_quantity: self.formData.stock_quantity,
                allow_stock_management: self.formData.allow_stock_management,
                auto_manage_stock: self.formData.auto_manage_stock,
                variant_attributes: self.formData.variant_attributes,
                allow_variants: self.formData.allow_variants,
                allow_downloads: self.formData.allow_downloads,
                show_on_store: self.formData.show_on_store,
                is_new: self.formData.is_new,
                is_featured: self.formData.is_featured
            };

            console.log(updateData);

            if(self.product['_links'].self.href){

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.product['_links'].self.href, updateData)
                    .then(({data}) => {

                        //  Reset the custom errors
                        self.customErrors = getCustomErrorFields();

                        //  Stop the loader
                        self.isUpdating = false;

                        //  Reset form fields so that the form does not show errors
                        setTimeout(()=>{
                            
                            self.$refs['formData'].resetFields();
                        
                            //  Update the form fields
                            self.updateFormFieldValues(data);

                            //  Store the current form data as the original form
                            self.storeOriginalFormData();

                            self.formHasChanged = self.checkIfFormHasChanged();


                        },10);

                        return data;
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        //  Stop loader
                        self.isUpdating = false;     
        
                    });

            }

        }

        return false;

    }
}