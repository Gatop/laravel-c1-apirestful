## ApiRest Example

![Alt Text](https://github.com/Gatop/laravel-c1-apirestful/blob/master/doc/doc-gif.gif)

## FrontEnd - Vue.js - Passport Authorization Example

![Alt Text](https://github.com/Gatop/laravel-c1-apirestful/blob/master/doc/doc-gif2.gif)

## ApiRest requests

 * <a href="https://github.com/Gatop/laravel-c1-apirestful/blob/master/doc/ApiRESTful%20Laravel.postman_collection.json">**Postman Requests**</a>

## Endpoints APIRest


	+---------+-----------------------------------------------+----------------------------------------+
	| Método  | URI                                           | Acción                                 |
	+---------+-----------------------------------------------+----------------------------------------+
	|GET      | buyers                                        | BuyerController@index                  |
	|GET      | buyers/{buyer}                                | BuyerController@show                   |
	|GET      | buyers/{buyer}/categories                     | BuyerCategoryController@index          |
	|GET      | buyers/{buyer}/products                       | BuyerProductController@index           |
	|GET      | buyers/{buyer}/sellers                        | BuyerSellerController@index            |
	|GET      | buyers/{buyer}/transactions                   | BuyerTransactionController@index       |
	|POST     | categories                                    | CategoryController@store               |
	|GET      | categories                                    | CategoryController@index               |
	|PUT|PATCH| categories/{category}                         | CategoryController@update              |
	|DELETE   | categories/{category}                         | CategoryController@destroy             |
	|GET      | categories/{category}                         | CategoryController@show                |
	|GET      | categories/{category}/buyers                  | CategoryBuyerController@index          |
	|GET      | categories/{category}/products                | CategoryProductController@index        |
	|GET      | categories/{category}/sellers                 | CategorySellerController@index         |
	|GET      | categories/{category}/transactions            | CategoryTransactionController@index    |
	|GET      | products                                      | ProductController@index                |
	|GET      | products/{product}                            | ProductController@show                 |
	|GET      | products/{product}/buyers                     | ProductBuyerController@index           |
	|POST     | products/{product}/buyers/{buyer}/transactions| ProductBuyerTransactionController@store|
	|GET      | products/{product}/categories                 | ProductCategoryController@index        |
	|DELETE   | products/{product}/categories/{category}      | ProductCategoryController@destroy      |
	|PUT|PATCH| products/{product}/categories/{category}      | ProductCategoryController@update       |
	|GET      | products/{product}/transactions               | ProductTransactionController@index     |
	|GET      | sellers                                       | SellerController@index                 |
	|GET      | sellers/{seller}                              | SellerController@show                  |
	|GET      | sellers/{seller}/buyers                       | SellerBuyerController@index            |
	|GET      | sellers/{seller}/categories                   | SellerCategoryController@index         |
	|GET      | sellers/{seller}/products                     | SellerProductController@index          |
	|POST     | sellers/{seller}/products                     | SellerProductController@store          |
	|DELETE   | sellers/{seller}/products/{product}           | SellerProductController@destroy        |
	|PUT|PATCH| sellers/{seller}/products/{product}           | SellerProductController@update         |
	|GET      | sellers/{seller}/transactions                 | SellerTransactionController@index      |
	|GET      | transactions                                  | TransactionController@index            |
	|GET      | transactions/{transaction}                    | TransactionController@show             |
	|GET      | transactions/{transaction}/categories         | TransactionCategoryController@index    |
	|GET      | transactions/{transaction}/sellers            | TransactionSellerController@index      |
	|POST     | users                                         | UserController@store                   |
	|GET      | users                                         | UserController@index                   |
	|GET      | users/verify/{token}                          | UserController@verify                  |
	|DELETE   | users/{user}                                  | UserController@destroy                 |
	|PUT|PATCH| users/{user}                                  | UserController@update                  |
	|GET      | users/{user}                                  | UserController@show                    |
	|GET      | users/{user}/resend                           | UserController@resend                  |
	+---------+-----------------------------------------------+----------------------------------------+	

## Model

![Alt Text](https://github.com/Gatop/laravel-c1-apirestful/blob/master/doc/DOC-Purchase-Sale-System.png)
