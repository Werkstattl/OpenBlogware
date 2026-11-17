const{Criteria:t}=Shopware.Data,o={computed:{globalCategoryRepository(){return this.repositoryFactory.create("werkl_blog_category")}},methods:{searchCategories(r){const e=new t(1,500);return e.setTerm(r),this.globalCategoryRepository.search(e)}}};export{o as default};
//# sourceMappingURL=index-BW_z9cil.js.map
