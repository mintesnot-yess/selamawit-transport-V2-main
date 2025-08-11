<script setup lang="ts">
import { computed, ref, type Component, useId } from "vue";
import { useMounted } from "@vueuse/core";
import {
  ChartCrosshair,
  ChartLegend,
  defaultColors,
} from "@/components/ui/chart";
import { cn } from "@/lib/utils";
import {
  type BulletLegendItemInterface,
  CurveType,
  Area,
  Line,
  Axis,
} from "@unovis/ts";
import { VisArea, VisLine, VisXYContainer, VisAxis } from "@unovis/vue";

const props = withDefaults(
  defineProps<{
    labels: string[];
    incomeData: number[];
    expenseData: number[];
    customTooltip?: Component;
    curveType?: CurveType;
    filterOpacity?: number;
    showXAxis?: boolean;
    showYAxis?: boolean;
    showTooltip?: boolean;
    showLegend?: boolean;
    showGridLine?: boolean;
    showGradiant?: boolean;
  }>(),
  {
    curveType: CurveType.MonotoneX,
    filterOpacity: 0.2,
    showXAxis: true,
    showYAxis: true,
    showTooltip: true,
    showLegend: true,
    showGridLine: true,
    showGradiant: true,
  }
);

const emits = defineEmits<{
  legendItemClick: [d: BulletLegendItemInterface, i: number];
}>();

// Processed chart data
const chartData = computed(() =>
  props.labels.map((label, i) => ({
    name: label,
    income: props.incomeData[i] ?? 0,
    expense: props.expenseData[i] ?? 0,
  }))
);

const categories = ["income", "expense"];
const colors = computed(() => defaultColors(categories.length));
const chartRef = useId();
const isMounted = useMounted();

const legendItems = ref<BulletLegendItemInterface[]>(
  categories.map((category, i) => ({
    name: category,
    color: colors.value[i],
    inactive: false,
  }))
);

function handleLegendItemClick(d: BulletLegendItemInterface, i: number) {
  emits("legendItemClick", d, i);
}
</script>

<template>
  <div
    :class="cn('w-full h-[400px] flex flex-col items-end', $attrs.class ?? '')"
  >
    <ChartLegend
      v-if="showLegend"
      v-model:items="legendItems"
      @legend-item-click="handleLegendItemClick"
    />

    <VisXYContainer
      :style="{ height: isMounted ? '100%' : 'auto' }"
      :margin="{ left: 20, right: 20 }"
      :data="chartData"
    >
      <svg width="0" height="0">
        <defs>
          <linearGradient
            v-for="(color, i) in colors"
            :id="`${chartRef}-color-${i}`"
            :key="i"
            x1="0"
            y1="0"
            x2="0"
            y2="1"
          >
            <template v-if="showGradiant">
              <stop offset="5%" :stop-color="color" stop-opacity="0.4" />
              <stop offset="95%" :stop-color="color" stop-opacity="0" />
            </template>
            <template v-else>
              <stop offset="0%" :stop-color="color" />
            </template>
          </linearGradient>
        </defs>
      </svg>

      <ChartCrosshair
        v-if="showTooltip"
        :colors="colors"
        :items="legendItems"
        index="name"
        :custom-tooltip="customTooltip"
      />

      <!-- Area curves -->
      <template v-for="(category, i) in categories" :key="category">
        <VisArea
          :x="(d) => d.name"
          :y="(d) => d[category]"
          color="auto"
          :curve-type="curveType"
          :attributes="{
            [Area.selectors.area]: {
              fill: `url(#${chartRef}-color-${i})`,
            },
          }"
          :opacity="
            legendItems.find((item) => item.name === category)?.inactive
              ? filterOpacity
              : 1
          "
        />
      </template>

      <!-- Line overlays -->
      <template v-for="(category, i) in categories" :key="`${category}-line`">
        <VisLine
          :x="(d) => d.name"
          :y="(d) => d[category]"
          :color="colors[i]"
          :curve-type="curveType"
          :attributes="{
            [Line.selectors.line]: {
              opacity: legendItems.find((item) => item.name === category)
                ?.inactive
                ? filterOpacity
                : 1,
            },
          }"
        />
      </template>

      <!-- X Axis with labels -->
      <VisAxis
        v-if="showXAxis"
        type="x"
        :tick-format="(v) => v"
        :grid-line="false"
        :tick-line="false"
        tick-text-color="hsl(var(--vis-text-color))"
      />

      <!-- Y Axis with numbers -->
      <VisAxis
        v-if="showYAxis"
        type="y"
        :tick-line="false"
        :tick-format="(v) => `${v}`"
        :domain-line="false"
        :grid-line="showGridLine"
        :attributes="{
          [Axis.selectors.grid]: {
            class: 'text-muted',
          },
        }"
        tick-text-color="hsl(var(--vis-text-color))"
      />

      <slot />
    </VisXYContainer>
  </div>
</template>
