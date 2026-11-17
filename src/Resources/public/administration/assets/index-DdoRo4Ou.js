const e='<div> <template v-for="componentSection in componentSections"> <slot v-bind="{ componentSection }"/> </template> </div>',{Store:t}=Shopware,n={template:e,props:{positionIdentifier:{type:String,required:!0}},computed:{componentSections(){return t.get("extensionComponentSections").identifier[this.positionIdentifier]??[]}}};export{n as default};
//# sourceMappingURL=index-DdoRo4Ou.js.map
