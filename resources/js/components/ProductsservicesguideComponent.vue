<template>

<v-app fill-height class="d-flex" height="100%">
	<v-main app fill-height class="d-flex" height="100%">
		<v-container fluid fill-height class="align-start ma-0 pa-0" style="all: unset;">	
			<v-container fluid class="ma-0 pa-0" style="all: unset;">

				<v-tabs
					v-model="tab"
					background-color="transparent"
					color="primary"
					grow
					show-arrows
					class="px-1">
					
					<v-tab>
						Категории товаров
					</v-tab>
					<v-tab>
						Товары
					</v-tab>
					<v-tab>
						Категории услуг
					</v-tab>
					<v-tab>
						Услуги
					</v-tab>
					
				</v-tabs>
			
					<v-tabs-items v-model="tab" class="px-1">
						<v-tab-item class="mt-5 mb-3">

							<v-data-table 
								:headers="headers"
								:items="desserts"
								:search="search"
								sort-by="id"
								class="elevation-1"
								mobile-breakpoint= "960"
								:loading="loading" 
								loading-text="Загрузка данных..."
								:footer-props="{itemsPerPageText: 'Строк на странице', itemsPerPageAllText: 'Все', 'items-per-page-options':[10, 20, -1]}"
								:header-props="{sortByText: 'Сортировка'}">
								
								<template v-slot:no-results>
									<span>Ничего не найдено</span>
								</template>
								
								<template v-slot:no-data>
									<span>Нет данных</span>
								</template>
								
								<template v-slot:[`footer.page-text`]="items"> 
									{{ items.pageStart }} - {{ items.pageStop }} из {{ items.itemsLength }}
								</template>
					  
								<template v-slot:top>

									<v-toolbar 
										flat>
									
										<v-toolbar-title>Категории товаров</v-toolbar-title>
										
										<v-divider
											class="mx-4"
											inset
											vertical>
										</v-divider>
								
										<v-text-field
											v-model="search"
											append-icon="mdi-magnify"
											label="Поиск"
											single-line
											hide-details
											spellcheck="false">
										</v-text-field>
										
										<v-spacer></v-spacer>
										
										<v-btn
											color="indigo"
											dark
											@click="newCategory"
											class="mb-2">
												Новая категория
										</v-btn>
										
										
									</v-toolbar>
								</template>
							
								<template v-slot:item.actions="{ item }">
									
									<v-icon
										small
										class="mr-1"
										@click="changeCategory(item)">
											mdi-pencil
									</v-icon>
								  
									<v-icon
										small
										class="mr-1"
										@click="deleteCategory(item)">
											mdi-delete
									</v-icon>
									
								</template>	
					
							</v-data-table>
					
						</v-tab-item>
				
						<v-tab-item class="mt-5 mb-3">
					
							<v-data-table 
								:headers="headers2"
								:items="desserts2"
								:search="search2"
								sort-by="id"
								class="elevation-1"
								mobile-breakpoint= "960"
								:loading="loading2" 
								loading-text="Загрузка данных..."
								:footer-props="{itemsPerPageText: 'Строк на странице', itemsPerPageAllText: 'Все', 'items-per-page-options':[50, 100]}"
								:header-props="{sortByText: 'Сортировка'}">
								
								<template v-slot:no-results>
									<span>Ничего не найдено</span>
								</template>
								
								<template v-slot:no-data>
									<span>Нет данных</span>
								</template>
								
								<template v-slot:[`footer.page-text`]="items"> 
									{{ items.pageStart }} - {{ items.pageStop }} из {{ items.itemsLength }}
								</template>
					  
								<template v-slot:top>

									<v-toolbar 
										flat>
									
										<v-toolbar-title>Товары</v-toolbar-title>
										
										<v-divider
											class="mx-4"
											inset
											vertical>
										</v-divider>
								
										<v-text-field
											v-model="search2"
											append-icon="mdi-magnify"
											label="Поиск"
											single-line
											hide-details
											spellcheck="false">
										</v-text-field>
										
										<v-spacer></v-spacer>
										
										<v-btn
											color="indigo"
											dark
											@click="newProduct"
											class="mb-2">
												Новый товар
										</v-btn>
										
										
									</v-toolbar>
								</template>
							
								<template v-slot:item.actions="{ item }">
									
									<v-icon
										small
										class="mr-1"
										@click="changeProduct(item)">
											mdi-pencil
									</v-icon>
								  
									<v-icon
										small
										class="mr-1"
										@click="deleteProduct(item)">
											mdi-delete
									</v-icon>
									
								</template>	
					
							</v-data-table>
					
						</v-tab-item>
				
						<v-tab-item class="mt-5 mb-3">
							
							<v-data-table 
								:headers="headers3"
								:items="desserts3"
								:search="search3"
								sort-by="id"
								class="elevation-1"
								mobile-breakpoint= "960"
								:loading="loading3" 
								loading-text="Загрузка данных..."
								:footer-props="{itemsPerPageText: 'Строк на странице', itemsPerPageAllText: 'Все', 'items-per-page-options':[10, 20, -1]}"
								:header-props="{sortByText: 'Сортировка'}">
								
								<template v-slot:no-results>
									<span>Ничего не найдено</span>
								</template>
								
								<template v-slot:no-data>
									<span>Нет данных</span>
								</template>
								
								<template v-slot:[`footer.page-text`]="items"> 
									{{ items.pageStart }} - {{ items.pageStop }} из {{ items.itemsLength }}
								</template>
					  
								<template v-slot:top>

									<v-toolbar 
										flat>
									
										<v-toolbar-title>Категории услуг</v-toolbar-title>
										
										<v-divider
											class="mx-4"
											inset
											vertical>
										</v-divider>
								
										<v-text-field
											v-model="search3"
											append-icon="mdi-magnify"
											label="Поиск"
											single-line
											hide-details
											spellcheck="false">
										</v-text-field>
										
										<v-spacer></v-spacer>
										
										<v-btn
											color="indigo"
											dark
											@click="newServCategory"
											class="mb-2">
												Новая категория
										</v-btn>
										
										
									</v-toolbar>
								</template>
							
								<template v-slot:item.actions="{ item }">
									
									<v-icon
										small
										class="mr-1"
										@click="changeServCategory(item)">
											mdi-pencil
									</v-icon>
								  
									<v-icon
										small
										class="mr-1"
										@click="deleteServCategory(item)">
											mdi-delete
									</v-icon>
									
								</template>	
					
							</v-data-table>
							
						</v-tab-item>
				
						<v-tab-item class="mt-5 mb-3">
							
							<v-data-table 
								:headers="headers4"
								:items="desserts4"
								:search="search4"
								sort-by="id"
								class="elevation-1"
								mobile-breakpoint= "960"
								:loading="loading4" 
								loading-text="Загрузка данных..."
								:footer-props="{itemsPerPageText: 'Строк на странице', itemsPerPageAllText: 'Все', 'items-per-page-options':[50, 100]}"
								:header-props="{sortByText: 'Сортировка'}">
								
								<template v-slot:no-results>
									<span>Ничего не найдено</span>
								</template>
								
								<template v-slot:no-data>
									<span>Нет данных</span>
								</template>
								
								<template v-slot:[`footer.page-text`]="items"> 
									{{ items.pageStart }} - {{ items.pageStop }} из {{ items.itemsLength }}
								</template>
					  
								<template v-slot:top>

									<v-toolbar 
										flat>
									
										<v-toolbar-title>Услуги</v-toolbar-title>
										
										<v-divider
											class="mx-4"
											inset
											vertical>
										</v-divider>
								
										<v-text-field
											v-model="search4"
											append-icon="mdi-magnify"
											label="Поиск"
											single-line
											hide-details
											spellcheck="false">
										</v-text-field>
										
										<v-spacer></v-spacer>
										
										<v-btn
											color="indigo"
											dark
											@click="newService"
											class="mb-2">
												Новая услуга
										</v-btn>
										
										
									</v-toolbar>
								</template>
							
								<template v-slot:item.actions="{ item }">
									
									<v-icon
										small
										class="mr-1"
										@click="changeService(item)">
											mdi-pencil
									</v-icon>
								  
									<v-icon
										small
										class="mr-1"
										@click="deleteService(item)">
											mdi-delete
									</v-icon>
									
								</template>	
					
							</v-data-table>
							
						</v-tab-item>
				
					</v-tabs-items>

				</v-container>
			
			
				<v-dialog
					v-model="dialog_cat"
					scrollable
					max-width="500px"
					class="dialogzindex">

					<v-card>
						<v-card-title>
							<span class="text-h5">{{ categoryTitle }}</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialog_cat = false">
								<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>

						<v-card-text>
							<v-container>
								<div v-if="errors_cat">
									<div v-for="(v, k) in errors_cat" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<v-row class="my-0 py-0 d-flex flex-column">
						  
									<v-form ref="form_cat" v-model="valid_cat">
									
										<v-col cols="12"  class="pt-0">
										
											<v-text-field
												v-model="form_cat.title"
												label="Наименование"
												v-on:keyup.enter="focusNext($event.target)"
												:rules="nameRules"
												required
												outline
												autofocus
												type="text"
												spellcheck="false">
											</v-text-field>
										
										</v-col>
										
										<v-col cols="12" class="pt-0">
																
											<v-text-field
												v-model="form_cat.percent"
												label="Процент (%)"
												@keydown.enter="save"
												:rules="percentRules"
												outline
												type="number"
												spellcheck="false">
											</v-text-field>
										
										</v-col>

									</v-form>
								
								</v-row>
							
							</v-container>
						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
					
							<v-btn color="info" @click="save" :loading="loading_cat" class="mb-2">
								Сохранить
							</v-btn>
						</v-card-actions>
					</v-card>

				</v-dialog>
			
			
				<v-dialog v-model="dialogDelete" max-width="500px" scrollable class="dialogzindex">
					<v-card>
					
						<v-card-title>
							<span class="text-h5">Удалить категорию?</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialogDelete = false">
									<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>
						
						<v-card-text>
						
							<v-container>
										  
								<div v-if="errors_del">
									<div v-for="(v, k) in errors_del" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<div class="black--text text-body-1 wrap-text">
									При удалении категории будут удалены данные о ней во всех связанных товарах. Подтвердите удаление.
								</div>
								
							</v-container>

						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
							
							<v-btn color="info" @click="deleteCategoryConfirm" :loading="loading_del" class="mb-2">
								Удалить
							</v-btn>

						</v-card-actions>
					</v-card>
				</v-dialog>
				
				
				<v-dialog
					v-model="dialog_prod"
					scrollable
					max-width="500px"
					class="dialogzindex">

					<v-card>
						<v-card-title>
							<span class="text-h5">{{ productTitle }}</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialog_prod = false">
								<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>

						<v-card-text>
							<v-container>
								<div v-if="errors_prod">
									<div v-for="(v, k) in errors_prod" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<v-row class="my-0 py-0 d-flex flex-column">
						  
									<v-form ref="form_prod" v-model="valid_prod">

										<v-col cols="12"  class="pt-0">

											<v-select
												v-model="form_prod.category"
												:items="desserts"
												item-text="title"
												item-value="id"
												persistent-hint
												autofocus
												clearable
												placeholder="Значение не выбрано"
												label="Категория товара"
												no-data-text="Нет данных">
											</v-select>
										
										</v-col>
						
										<v-col cols="12"  class="pt-0">
										
											<v-text-field
												v-model="form_prod.product_title"
												label="Наименование"
												v-on:keyup.enter="focusNext($event.target)"
												:rules="nameRules"
												required
												outline
												type="text"
												spellcheck="false">
											</v-text-field>
										
										</v-col>
										
										
										<v-col cols="12"  class="pt-0">
										
											<v-text-field
												v-model="form_prod.product_edizm"
												label="Ед. измерения"
												v-on:keyup.enter="focusNext($event.target)"
												:rules="nameRules"
												required
												outline
												type="text"
												spellcheck="false">
											</v-text-field>
										
										</v-col>
										
										
										<v-col cols="12" class="pt-0">
																
											<v-text-field
												v-model="form_prod.product_in_price"
												label="Цена закупки"
												v-on:keyup.enter="focusNext($event.target)"
												:rules="[...nameRules, ...percentRules]"
												outline
												type="number"
												spellcheck="false">
											</v-text-field>
										
										</v-col>
										
										
										<v-col cols="12" class="pt-0">
																
											<v-text-field
												v-model="form_prod.product_out_price"
												label="Цена"
												@keydown.enter="saveprod"
												:rules="[...nameRules, ...percentRules]"
												outline
												type="number"
												spellcheck="false">
											</v-text-field>
										
										</v-col>

									</v-form>
								
								</v-row>
							
							</v-container>
						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
					
							<v-btn color="info" @click="saveprod" :loading="loading_prod" class="mb-2">
								Сохранить
							</v-btn>
						</v-card-actions>
					</v-card>

				</v-dialog>
				
				<v-dialog v-model="dialogProdDelete" max-width="500px" scrollable class="dialogzindex">
					<v-card>
					
						<v-card-title>
							<span class="text-h5">Удалить товар?</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialogProdDelete = false">
									<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>
						
						<v-card-text>
						
							<v-container>
										  
								<div v-if="errors_prod_del">
									<div v-for="(v, k) in errors_prod_del" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<div class="black--text text-body-1 wrap-text">
									Подтвердите удаление товара.
								</div>
								
							</v-container>

						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
							
							<v-btn color="info" @click="deleteProductConfirm" :loading="loading_prod_del" class="mb-2">
								Удалить
							</v-btn>

						</v-card-actions>
					</v-card>
				</v-dialog>
				
				
				<v-dialog
					v-model="dialog_serv_cat"
					scrollable
					max-width="500px"
					class="dialogzindex">

					<v-card>
						<v-card-title>
							<span class="text-h5">{{ servCatTitle }}</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialog_serv_cat = false">
								<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>

						<v-card-text>
							<v-container>
								<div v-if="errors_serv_cat">
									<div v-for="(v, k) in errors_serv_cat" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<v-row class="my-0 py-0 d-flex flex-column">
						  
									<v-form ref="form_serv_cat" v-model="valid_serv_cat">
									
										<v-col cols="12"  class="pt-0">
										
											<v-text-field
												v-model="form_serv_cat.title"
												label="Наименование"
												v-on:keyup.enter="focusNext($event.target)"
												:rules="nameRules"
												required
												outline
												autofocus
												type="text"
												spellcheck="false">
											</v-text-field>
										
										</v-col>
										
										<v-col cols="12" class="pt-0">
																
											<v-text-field
												v-model="form_serv_cat.percent"
												label="Процент (%)"
												@keydown.enter="saveservcat"
												:rules="percentRules"
												outline
												type="number"
												spellcheck="false">
											</v-text-field>
										
										</v-col>

									</v-form>
								
								</v-row>
							
							</v-container>
						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
					
							<v-btn color="info" @click="saveservcat" :loading="loading_serv_cat" class="mb-2">
								Сохранить
							</v-btn>
						</v-card-actions>
					</v-card>

				</v-dialog>
				
				
				<v-dialog v-model="dialogServCatDelete" max-width="500px" scrollable class="dialogzindex">
					<v-card>
					
						<v-card-title>
							<span class="text-h5">Удалить категорию?</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialogServCatDelete = false">
									<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>
						
						<v-card-text>
						
							<v-container>
										  
								<div v-if="errors_serv_cat_del">
									<div v-for="(v, k) in errors_serv_cat_del" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<div class="black--text text-body-1 wrap-text">
									При удалении категории будут удалены данные о ней во всех связанных услугах. Подтвердите удаление.
								</div>
								
							</v-container>

						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
							
							<v-btn color="info" @click="deleteServCatConfirm" :loading="loading_serv_cat_del" class="mb-2">
								Удалить
							</v-btn>

						</v-card-actions>
					</v-card>
				</v-dialog>
				
				
				<v-dialog
					v-model="dialog_serv"
					scrollable
					max-width="500px"
					class="dialogzindex">

					<v-card>
						<v-card-title>
							<span class="text-h5">{{ serviceTitle }}</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialog_serv = false">
								<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>

						<v-card-text>
							<v-container>
								<div v-if="errors_serv">
									<div v-for="(v, k) in errors_serv" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<v-row class="my-0 py-0 d-flex flex-column">
						  
									<v-form ref="form_serv" v-model="valid_serv">

										<v-col cols="12"  class="pt-0">

											<v-select
												v-model="form_serv.category"
												:items="desserts3"
												item-text="title"
												item-value="id"
												persistent-hint
												autofocus
												clearable
												placeholder="Значение не выбрано"
												label="Категория услуги"
												no-data-text="Нет данных">
											</v-select>
										
										</v-col>
						
										<v-col cols="12"  class="pt-0">
										
											<v-text-field
												v-model="form_serv.service_title"
												label="Наименование"
												v-on:keyup.enter="focusNext($event.target)"
												:rules="nameRules"
												required
												outline
												type="text"
												spellcheck="false">
											</v-text-field>
										
										</v-col>

										<v-col cols="12" class="pt-0">
																
											<v-text-field
												v-model="form_serv.service_price"
												label="Цена"
												@keydown.enter="saveserv"
												:rules="[...nameRules, ...percentRules]"
												outline
												type="number"
												spellcheck="false">
											</v-text-field>
										
										</v-col>

									</v-form>
								
								</v-row>
							
							</v-container>
						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
					
							<v-btn color="info" @click="saveserv" :loading="loading_serv" class="mb-2">
								Сохранить
							</v-btn>
						</v-card-actions>
					</v-card>

				</v-dialog>
				
				<v-dialog v-model="dialogServDelete" max-width="500px" scrollable class="dialogzindex">
					<v-card>
					
						<v-card-title>
							<span class="text-h5">Удалить товар?</span>
							<v-spacer></v-spacer>
							<v-btn
								icon
								@click="dialogServDelete = false">
									<v-icon>mdi-close</v-icon>
							</v-btn>  
						</v-card-title>
						
						<v-card-text>
						
							<v-container>
										  
								<div v-if="errors_serv_del">
									<div v-for="(v, k) in errors_serv_del" :key="k">
										<v-alert type="error" v-for="error in v" :key="error">{{ error }}</v-alert>
									</div>
								</div>
								
								<div class="black--text text-body-1 wrap-text">
									Подтвердите удаление услуги.
								</div>
								
							</v-container>

						</v-card-text>

						<v-card-actions>
							<v-spacer></v-spacer>
							
							<v-btn color="info" @click="deleteServiceConfirm" :loading="loading_serv_del" class="mb-2">
								Удалить
							</v-btn>

						</v-card-actions>
					</v-card>
				</v-dialog>

		</v-container>
	</v-main>
</v-app>
	
</template>



<script>
	
    export default {
        data: () => ({
			
			tab: null,
			
			nameRules: [],
			percentRules: [],

			loading: false,			
			search: '',
			dialog_cat: false,
			categoryTitle: '',
			errors_cat: '',
			valid_cat: true,
			loading_cat: false,
			categorytodelid: 0,
			errors_del: '',
			loading_del: false,
			
			form_cat: {},
			default_form_cat_item: {
				id: '',
				title: '',
				percent: '',	
			},

			dialogDelete: false,
			headers: [
				{ text: 'ID', align: 'start', sortable: true, value: 'id', filterable: false },
				{ text: 'Наименование', value: 'title', sortable: true, filterable: true },
				{ text: 'Процент', value: 'percent', sortable: false, filterable: false  },
				{ text: 'Действия', value: 'actions', sortable: false, filterable: false },
			], 
			desserts: [],
			
			
			loading2: false,			
			search2: '',
			dialog_prod: false,
			productTitle: '',
			errors_prod: '',
			valid_prod: true,
			loading_prod: false,
			producttodelid: 0,
			errors_prod_del: '',
			loading_prod_del: false,
			
			form_prod: {},
			default_form_prod_item: {
				id: '',
				category: '',
				product_title: '',
				product_edizm: '',
				product_in_price: '',
				product_out_price: '',
			},

			dialogProdDelete: false,
			headers2: [
				{ text: 'ID', align: 'start', sortable: true, value: 'id', filterable: false },
				{ text: 'Категория', value: 'categoryname', sortable: true, filterable: true },
				{ text: 'Наименование', value: 'product_title', sortable: true, filterable: true },
				{ text: 'Ед. измерения', value: 'product_edizm', sortable: false, filterable: false },
				{ text: 'Цена закупки', value: 'product_in_price', sortable: false, filterable: false  },
				{ text: 'Цена', value: 'product_out_price', sortable: false, filterable: false },
				{ text: 'Действия', value: 'actions', sortable: false, filterable: false },
			], 
			desserts2: [],
			
			loading3: false,			
			search3: '',
			dialog_serv_cat: false,
			servCatTitle: '',
			errors_serv_cat: '',
			valid_serv_cat: true,
			loading_serv_cat: false,
			servicecattodelid: 0,
			errors_serv_cat_del: '',
			loading_serv_cat_del: false,
			
			form_serv_cat: {},
			default_form_serv_cat_item: {
				id: '',
				title: '',
				percent: '',
			},

			dialogServCatDelete: false,
			headers3: [
				{ text: 'ID', align: 'start', sortable: true, value: 'id', filterable: false },
				{ text: 'Наименование', value: 'title', sortable: true, filterable: true },
				{ text: 'Процент', value: 'percent', sortable: false, filterable: false  },
				{ text: 'Действия', value: 'actions', sortable: false, filterable: false },
			], 
			desserts3: [],
			
			
			loading4: false,			
			search4: '',
			dialog_serv: false,
			serviceTitle: '',
			errors_serv: '',
			valid_serv: true,
			loading_serv: false,
			servicetodelid: 0,
			errors_serv_del: '',
			loading_serv_del: false,
			
			form_serv: {},
			default_form_serv_item: {
				id: '',
				category: '',
				service_title: '',
				service_price: '',
			},

			dialogServDelete: false,
			headers4: [
				{ text: 'ID', align: 'start', sortable: true, value: 'id', filterable: false },
				{ text: 'Категория', value: 'categoryname', sortable: true, filterable: true },
				{ text: 'Наименование', value: 'service_title', sortable: true, filterable: true },
				{ text: 'Цена', value: 'service_price', sortable: false, filterable: false },
				{ text: 'Действия', value: 'actions', sortable: false, filterable: false },
			], 
			desserts4: [],
			
		}),
		
		mounted() {
			// Добавляем стили Vuetify только для этого компонента
			let style = document.createElement('link');
			style.type = "text/css";
			style.rel = "stylesheet";
			style.href = 'css/vuetify.css';
			document.head.appendChild(style);
		},
		
		watch: {
			dialog_cat (val) {
				val || this.closecatdialog()
			},
			dialogDelete (val) {
				val || this.closeDelete()
			},
			'this.form_cat.title' (val) {
				this.nameRules = []
			},
			'this.form_cat.rercent' (val) {
				this.percentRules = []
			},
			dialog_prod (val) {
				val || this.closeproddialog()
			},
			dialogProdDelete (val) {
				val || this.closeProdDelete()
			},
			'this.form_prod.product_title' (val) {
				this.nameRules = []
			},
			'this.form_prod.product_edizm' (val) {
				this.nameRules = []
			},
			'this.form_prod.product_in_price' (val) {
				this.percentRules = []
			},
			'this.form_prod.product_out_price' (val) {
				this.percentRules = []
			},
			dialog_serv_cat (val) {
				val || this.closeservcatdialog()
			},
			dialogServCatDelete (val) {
				val || this.closeServCatDelete()
			},
			'this.form_serv_cat.title' (val) {
				this.nameRules = []
			},
			'this.form_serv_cat.rercent' (val) {
				this.percentRules = []
			},
			dialog_serv (val) {
				val || this.closeservdialog()
			},
			dialogServDelete (val) {
				val || this.closeServDelete()
			},
			'this.form_serv.product_title' (val) {
				this.nameRules = []
			},
			'this.form_serv.service_price' (val) {
				this.percentRules = []
			},
			
		},
		
		computed: {
			
		}, 
		
		methods: {

			newCategory () {
				
				this.categoryTitle = 'Новая категория';
				this.dialog_cat = true
				this.form_cat = Object.assign({}, this.default_form_cat_item)				
				this.errors_cat = '';  
				
			},
			
			changeCategory (item) {	

				this.categoryTitle = 'Изменить категорию';
				this.dialog_cat = true
				this.form_cat = Object.assign({}, this.default_form_cat_item)						
				this.errors_cat = '';
				this.form_cat = Object.assign({}, this.desserts.find(o => o.id === item.id));				
				
			},
			
			closecatdialog () {
				this.dialog_cat = false
				this.nameRules = [];
				this.percentRules = [];
			},
			
			save () {	  
			
				this.nameRules = [
					value => !!value || 'Поле обязательно для заполнения',
				]

				this.percentRules = [
					value => (+value == +value && !'') ||'Введите числовое значение',
				]				
				
				setTimeout(function () {
					if (this.$refs.form_cat.validate() == true && !this.loading_cat){
						
						this.loading_cat = true;						
						axios.post('guides/addproductcategory', this.form_cat)
						.then((response) => {
							this.loading_cat = false;
							this.errors_cat = '';
							this.closecatdialog()
							this.setCategoriesList();
						})
						.catch(error => {
							this.loading_cat = false;
							if (error.response.data.errors)
							this.errors_cat = error.response.data.errors;
						});
								
					}  
				}.bind(this))

			},
			
			setCategoriesList () {
				
				this.loading = true;
				axios.get('guides/getproductcategories')
				.then((response) => {
											
					this.desserts = response.data;

				})
				.catch(error => {})
				.finally(() => {this.loading = false;}); 
			},
			
			deleteCategory (item) {	

				this.errors_del = '';
				this.categorytodelid = item.id;
				this.dialogDelete = true
								
			},
			
			deleteCategoryConfirm () {
				setTimeout(function () {
					this.loading_del = true;				
					
					axios.post('guides/deleteproductcategory', this.desserts.find(o => o.id === this.categorytodelid))
					.then((response) => {
						this.loading_del = false;
						this.errors_del = '';
						this.categorytodelid = 0
						this.closeDelete();
						this.setCategoriesList ();
					})
					.catch(error => {
						this.loading_del = false;
						
						if (error.response.data.errors)
						this.errors_del = error.response.data.errors;
					});
					
				}.bind(this))
			},
			
			
			closeDelete () {
				this.dialogDelete = false
			},
			
			
			newServCategory () {
				
				this.servCatTitle = 'Новая категория';
				this.dialog_serv_cat = true
				this.form_serv_cat = Object.assign({}, this.default_form_serv_cat_item)				
				this.errors_serv_cat = '';  
				
			},
			
			changeServCategory (item) {	

				this.servCatTitle = 'Изменить категорию';
				this.dialog_serv_cat = true
				this.form_serv_cat = Object.assign({}, this.default_form_serv_cat_item)						
				this.errors_serv_cat = '';
				this.form_serv_cat = Object.assign({}, this.desserts3.find(o => o.id === item.id));				
				
			},
			
			closeservcatdialog () {
				this.dialog_serv_cat = false
				this.nameRules = [];
				this.percentRules = [];
			},
			
			saveservcat () {	  
			
				this.nameRules = [
					value => !!value || 'Поле обязательно для заполнения',
				]

				this.percentRules = [
					value => (+value == +value && !'') ||'Введите числовое значение',
				]				
				
				setTimeout(function () {
					if (this.$refs.form_serv_cat.validate() == true && !this.loading_serv_cat){
						
						this.loading_serv_cat = true;						
						axios.post('guides/addservicecategory', this.form_serv_cat)
						.then((response) => {
							this.loading_serv_cat = false;
							this.errors_serv_cat = '';
							this.closeservcatdialog()
							this.setServCategoriesList();
						})
						.catch(error => {
							this.loading_serv_cat = false;
							if (error.response.data.errors)
							this.errors_serv_cat = error.response.data.errors;
						});
								
					}  
				}.bind(this))

			},
			
			
			setServCategoriesList() {
				
				this.loading3 = true;
				axios.get('guides/getservicecategories')
				.then((response) => {
											
					this.desserts3 = response.data;

				})
				.catch(error => {})
				.finally(() => {this.loading3 = false;}); 
				
			},
			
			
			deleteServCategory (item) {	

				this.errors_serv_cat_del = '';
				this.servicecattodelid = item.id;
				this.dialogServCatDelete = true
								
			},
			
			deleteServCatConfirm () {
				setTimeout(function () {
					this.loading_serv_cat_del = true;				
					
					axios.post('guides/deleteservicecategory', this.desserts3.find(o => o.id === this.servicecattodelid))
					.then((response) => {
						this.loading_serv_cat_del = false;
						this.errors_serv_cat_del = '';
						this.servicecattodelid = 0
						this.closeServCatDelete();
						this.setServCategoriesList();
					})
					.catch(error => {
						this.loading_serv_cat_del = false;
						
						if (error.response.data.errors)
						this.errors_serv_cat_del = error.response.data.errors;
					});
					
				}.bind(this))
			},
			
			
			closeServCatDelete () {
				this.dialogServCatDelete = false
			},
			

			newProduct () {
				
				this.productTitle = 'Новый товар';
				this.dialog_prod = true
				this.form_prod = Object.assign({}, this.default_form_prod_item)				
				this.errors_prod = '';  
				
			},
			
			changeProduct (item) {
				
				this.productTitle = 'Изменить товар';
				this.form_prod = Object.assign({}, this.default_form_prod_item)						
				this.errors_prod = '';
				this.form_prod = Object.assign({}, this.desserts2.find(o => o.id === item.id)); 				
				this.dialog_prod = true
				
			},
			
			
			closeproddialog () {
				this.dialog_prod = false
				this.nameRules = [];
				this.percentRules = [];
			},
			
	
			saveprod () {	  
			
				this.nameRules = [
					value => !!value || 'Поле обязательно для заполнения',
				]

				this.percentRules = [
					value => (+value == +value && !'') ||'Введите числовое значение',
				]				
				
				setTimeout(function () {
					if (this.$refs.form_prod.validate() == true && !this.loading_prod){
						
						this.loading_prod = true;						
						axios.post('guides/addproduct', this.form_prod)
						.then((response) => {
							this.loading_prod = false;
							this.errors_prod = '';
							this.closeproddialog()
							this.setProductList();
						})
						.catch(error => {
							this.loading_prod = false;
							if (error.response.data.errors)
							this.errors_prod = error.response.data.errors;
						});
								
					}  
				}.bind(this))

			},
			
			setProductList () {
				
				this.loading2 = true;
				axios.get('guides/getproducts')
				.then((response) => {
											
					this.desserts2 = response.data;

				})
				.catch(error => {})
				.finally(() => {this.loading2 = false;});
			},
			
			deleteProduct (item) {	

				this.errors_prod_del = '';
				this.producttodelid = item.id;
				this.dialogProdDelete = true
								
			},
			
			deleteProductConfirm () {
				setTimeout(function () {
					this.loading_prod_del = true;				
					
					axios.post('guides/deleteproduct', this.desserts2.find(o => o.id === this.producttodelid))
					.then((response) => {
						this.loading_prod_del = false;
						this.errors_prod_del = '';
						this.producttodelid = 0
						this.closeProdDelete();
						this.setProductList ();
					})
					.catch(error => {
						this.loading_prod_del = false;
						
						if (error.response.data.errors)
						this.errors_prod_del = error.response.data.errors;
					});
					
				}.bind(this))
			},
			
			
			closeProdDelete () {
				this.dialogProdDelete = false
			},
			
			
			newService () {
				
				this.serviceTitle = 'Новая услуга';
				this.dialog_serv = true
				this.form_serv = Object.assign({}, this.default_form_serv_item)				
				this.errors_serv = '';  
				
			},
			
			changeService (item) {
				
				this.serviceTitle = 'Изменить товар';
				this.form_serv = Object.assign({}, this.default_form_serv_item)						
				this.errors_serv = '';
				this.form_serv = Object.assign({}, this.desserts4.find(o => o.id === item.id)); 				
				this.dialog_serv = true
				
			},
			

			closeservdialog () {
				this.dialog_serv = false
				this.nameRules = [];
				this.percentRules = [];
			},
			
			saveserv () {	  
			
				this.nameRules = [
					value => !!value || 'Поле обязательно для заполнения',
				]

				this.percentRules = [
					value => (+value == +value && !'') ||'Введите числовое значение',
				]	

				setTimeout(function () {
					if (this.$refs.form_serv.validate() == true && !this.loading_serv){
						
						this.loading_serv = true;						
						axios.post('guides/addservice', this.form_serv)
						.then((response) => {
							this.loading_serv = false;
							this.errors_serv = '';
							this.closeservdialog()
							this.setServiceList();
						})
						.catch(error => {
							this.loading_serv = false;
							if (error.response.data.errors)
							this.errors_serv = error.response.data.errors;
						});
								
					}  
				}.bind(this))

			},
			
			setServiceList () {
				
				this.loading4 = true;
				axios.get('guides/getservices')
				.then((response) => {
											
					this.desserts4 = response.data;

				})
				.catch(error => {})
				.finally(() => {this.loading4 = false;});
			},
			
			deleteService (item) {	

				this.errors_serv_del = '';
				this.servicetodelid = item.id;
				this.dialogServDelete = true
								
			},
			
			deleteServiceConfirm () {
				setTimeout(function () {
					this.loading_serv_del = true;				
					
					axios.post('guides/deleteservice', this.desserts4.find(o => o.id === this.servicetodelid))
					.then((response) => {
						this.loading_serv_del = false;
						this.errors_serv_del = '';
						this.servicetodelid = 0
						this.closeServDelete();
						this.setServiceList ();
					})
					.catch(error => {
						this.loading_serv_del = false;
						
						if (error.response.data.errors)
						this.errors_serv_del = error.response.data.errors;
					});
					
				}.bind(this))
			},
						
			closeServDelete () {
				this.dialogServDelete = false
			},
			
			
			focusNext(elem) {
				const currentIndex = Array.from(elem.form.elements).indexOf(elem);
				elem.form.elements.item(
					currentIndex < elem.form.elements.length - 1 ?
					currentIndex + 1 : 0
				).focus(); 
			},
		
		},
		
		created () {
			this.setCategoriesList();
			this.setProductList();
			this.setServCategoriesList();
			this.setServiceList();
		},
    }
</script>


<style>

  .v-application--wrap {
    min-height: 0vh !important;
  }
  
  .v-menu {
	  z-index: 10000;
  }
  
  .dialogzindex {
	  z-index: 10000;
  }
  
</style>






