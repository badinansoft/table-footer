<template>
  <table
    v-if="resources.length > 0"
    class="table w-full"
    :class="[
      `table-${resourceInformation.tableStyle}`,
      resourceInformation.showColumnBorders ? 'table-grid' : '',
    ]"
    cellpadding="0"
    cellspacing="0"
    data-testid="resource-table"
  >
    <thead>
      <tr>
        <!-- Select Checkbox -->
        <th class="w-16" v-if="shouldShowCheckboxes">&nbsp;</th>

        <!-- Field Names -->
        <th v-for="field in fields" :class="`text-${field.textAlign}`">
          <sortable-icon
            @sort="requestOrderByChange(field)"
            @reset="resetOrderBy(field)"
            :resource-name="resourceName"
            :uri-key="field.sortableUriKey"
            v-if="sortable && field.sortable"
          >
            {{ field.indexName }}
          </sortable-icon>

          <span v-else>{{ field.indexName }}</span>
        </th>

        <!-- Actions, View, Edit, Delete -->
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="(resource, index) in resources"
        @actionExecuted="$emit('actionExecuted')"
        :testId="`${resourceName}-items-${index}`"
        :key="resource.id.value"
        :delete-resource="deleteResource"
        :restore-resource="restoreResource"
        is="resource-table-row"
        :resource="resource"
        :resource-name="resourceName"
        :relationship-type="relationshipType"
        :via-relationship="viaRelationship"
        :via-resource="viaResource"
        :via-resource-id="viaResourceId"
        :via-many-to-many="viaManyToMany"
        :checked="selectedResources.indexOf(resource) > -1"
        :actions-are-available="actionsAreAvailable"
        :actions-endpoint="actionsEndpoint"
        :should-show-checkboxes="shouldShowCheckboxes"
        :update-selection-status="updateSelectionStatus"
      />
    </tbody>

      <tfoot v-if="calculate.length>0">
            <tr>
                <th class="w-16" v-if="shouldShowCheckboxes">&nbsp;</th>
                <th v-for="field in fields" :class="`text-${field.textAlign}`">{{field.title}}</th>
                <!-- Actions, View, Edit, Delete -->
                <th>&nbsp;</th>
            </tr>

            <tr>
                <th class="w-16" v-if="shouldShowCheckboxes">&nbsp;</th>
                <th v-for="field in fields" :class="`text-${field.textAlign}`">
                    {{field.prefix}}
                    <span v-if="field.calculate">{{calculate.find(o=>o.indexName===field.sortableUriKey)?calculate.find(o=>o.indexName===field.sortableUriKey).value:0}}</span>
                    {{field.postfix}}
                </th>
                <!-- Actions, View, Edit, Delete -->
                <th>&nbsp;</th>
            </tr>
      </tfoot>
  </table>
</template>

<script>
import { InteractsWithResourceInformation } from 'laravel-nova'

export default {
  mixins: [InteractsWithResourceInformation],

  props: {
    authorizedToRelate: {
      type: Boolean,
      required: true,
    },
    resourceName: {
      default: null,
    },
    resources: {
      default: [],
    },
    singularName: {
      type: String,
      required: true,
    },
    selectedResources: {
      default: [],
    },
    selectedResourceIds: {},
    shouldShowCheckboxes: {
      type: Boolean,
      default: false,
    },
    actionsAreAvailable: {
      type: Boolean,
      default: false,
    },
    viaResource: {
      default: null,
    },
    viaResourceId: {
      default: null,
    },
    viaRelationship: {
      default: null,
    },
    relationshipType: {
      default: null,
    },
    updateSelectionStatus: {
      type: Function,
    },
    actionsEndpoint: {
      default: null,
    },
    sortable: {
      type: Boolean,
      default: false,
    },
  },

  data: () => ({
    selectAllResources: false,
    selectAllMatching: false,
    resourceCount: null,
    calculate: [],
  }),

    async created(){
        if(this.resources.length>0 && this.resources[0].fields){
            this.calculate = [];
            this.resources[0].fields.forEach(field => {
                if (field.calculate) {
                    this.putFiledCalculate({
                        indexName: field.sortableUriKey,
                        calculate: field.calculate,
                        value: 0,
                    });
                }
            });
            this.fetchCalculate();
        }
    },

  methods: {



    /**
     * Delete the given resource.
     */
    deleteResource(resource) {
      this.$emit('delete', [resource])
    },

    /**
     * Restore the given resource.
     */
    restoreResource(resource) {
      this.$emit('restore', [resource])
    },

    /**
     * Broadcast that the ordering should be updated.
     */
    requestOrderByChange(field) {
      this.$emit('order', field)
    },

    /**
     * Broadcast that the ordering should be reset.
     */
    resetOrderBy(field) {
      this.$emit('reset-order-by', field)
    },

      putFiledCalculate(field) {
          this.calculate.push(field);
      },

      fetchCalculate() {

          let fields = {
              ...this.resourceRequestQueryString,
              calculate: this.calculate
          };
          Nova.request()
            .get(`/nova-vendor/table-footer/calculate/${this.resourceName}`, {
                params: fields,
            })
            .then(response => {
                if (response.data) {
                    this.calculate.forEach(field => {
                        if (response.data[field.indexName]) {
                            this.calculate.find(o=>o.indexName===field.indexName).value = response.data[field.indexName];
                        }
                    });
                }
            })
            .catch(error => {
                throw new Error(error.response.data.message);
            });
      },
  },

  computed: {


    /**
     * Get all of the available fields for the resources.
     */
    fields() {
      if (this.resources) {
        return this.resources[0].fields
      }
    },


      /**
       * Get the current search value from the query string.
       */
      currentSearch() {
          return this.$route.query[this.searchParameter] || ''
      },

      /**
       * Get the name of the search query string variable.
       */
      searchParameter() {
          return this.viaRelationship
              ? this.viaRelationship + '_search'
              : this.resourceName + '_search'
      },

    /**
     * Determine if the current resource listing is via a many-to-many relationship.
     */
    viaManyToMany() {
      return (
        this.relationshipType == 'belongsToMany' ||
        this.relationshipType == 'morphToMany'
      )
    },

    /**
     * Determine if the current resource listing is via a has-one relationship.
     */
    viaHasOne() {
      return (
        this.relationshipType == 'hasOne' || this.relationshipType == 'morphOne'
      )
    },

      /**
       * Build the resource request query string.
       */
      resourceRequestQueryString() {
          return {
              search: this.currentSearch,
              filters: this.encodedFilters,
              orderBy: this.currentOrderBy,
              orderByDirection: this.currentOrderByDirection,
              perPage: this.currentPerPage,
              trashed: this.currentTrashed,
              page: this.currentPage,
              viaResource: this.viaResource,
              viaResourceId: this.viaResourceId,
              viaRelationship: this.viaRelationship,
              viaResourceRelationship: this.viaResourceRelationship,
              relationshipType: this.relationshipType,
          }
      },

      /**
       * Return the currently encoded filter string from the store
       */
      encodedFilters() {
          return this.$store.getters[`${this.resourceName}/currentEncodedFilters`]
      },

      /**
       * Get the current order by value from the query string.
       */
      currentOrderBy() {
          return this.$route.query[this.orderByParameter] || ''
      },
  },
}
</script>
