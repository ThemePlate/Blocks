(()=>{"use strict";var e={n:t=>{var l=t&&t.__esModule?()=>t.default:()=>t;return e.d(l,{a:l}),l},d:(t,l)=>{for(var n in l)e.o(l,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:l[n]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.wp.blocks,l=window.React,n=window.wp.i18n,a=window.wp.blockEditor,r=window.wp.components,o=window.wp.data,c=window.wp.element,s=window.wp.hooks,i=window.wp.serverSideRender;var u=e.n(i);const m=window.ThemePlate_Blocks||{ajax_url:"",_wpnonce:"",collection:{}},p=(e,t,o)=>{const[s,i]=(0,c.useState)(!1);switch(e.type){default:case"text":case"time":case"email":case"url":case"date":case"number":return(0,l.createElement)(r.TextControl,{type:e.type,label:e.title,help:e?.help||"",value:t[e.key],onChange:t=>o({[e.key]:t})});case"textarea":return(0,l.createElement)(r.TextareaControl,{label:e.title,help:e?.help||"",value:t[e.key],onChange:t=>o({[e.key]:t})});case"select":case"select2":return(0,l.createElement)(r.SelectControl,{label:e.title,help:e?.help||"",value:t[e.key],options:e.options,multiple:e.multiple,onChange:t=>o({[e.key]:t})});case"radiolist":case"radio":return(0,l.createElement)(r.RadioControl,{label:e.title,help:e?.help||"",selected:t[e.key],options:e.options,onChange:t=>o({[e.key]:t})});case"checklist":case"checkbox":return(0,l.createElement)(r.BaseControl,{help:e?.help||""},(0,l.createElement)(r.BaseControl.VisualLabel,null,e.title),0===e.options.length&&(0,l.createElement)(r.CheckboxControl,{checked:"true"===t[e.key],onChange:t=>o({[e.key]:t.toString()})}),0!==e.options.length&&(0,l.createElement)(c.Fragment,null,e.options.map((n=>(0,l.createElement)(r.CheckboxControl,{key:n.value,label:n.label,checked:t[e.key].includes(n.value),onChange:l=>{o({[e.key]:e.options.filter((({value:a})=>n.value!==a?t[e.key].includes(a):l)).map((({value:e})=>e))})}})))));case"color":return(0,l.createElement)(r.BaseControl,{help:e?.help||""},(0,l.createElement)(r.BaseControl.VisualLabel,null,e.title),(0,l.createElement)(c.Fragment,null,(0,l.createElement)(r.Flex,{gap:4,align:"flex-start",justify:"flex-start"},(0,l.createElement)(r.FlexItem,null,(0,l.createElement)(r.Button,{variant:"secondary",onClick:()=>i(!0)},"Pick")),(0,l.createElement)(r.FlexItem,null,(0,l.createElement)(r.ColorIndicator,{colorValue:t[e.key],className:"themeplate-color-indicator"}))),s&&(0,l.createElement)(r.Modal,{focusOnMount:!0,shouldCloseOnEsc:!0,shouldCloseOnClickOutside:!0,title:(0,n.__)("Insert/edit color"),onRequestClose:()=>i(!1)},(0,l.createElement)(r.ColorPicker,{color:t[e.key],onChange:t=>o({[e.key]:t}),enableAlpha:!0}))));case"range":return(0,l.createElement)(r.RangeControl,{label:e.title,help:e?.help||"",value:parseInt(t[e.key]),onChange:t=>o({[e.key]:t.toString()}),afterIcon:(0,l.createElement)("strong",null,t[e.key]),withInputField:!1});case"html":return(0,l.createElement)(c.RawHTML,null,t[e.key]);case"group":return(0,l.createElement)(r.BaseControl,{help:e?.help||""},(0,l.createElement)(r.BaseControl.VisualLabel,null,e.title),(0,l.createElement)(d,{list:e.fields,attributes:t[e.key],setAttributes:l=>{o({[e.key]:{...t[e.key],...l}})}}));case"link":return(0,l.createElement)(r.BaseControl,{help:e?.help||""},(0,l.createElement)(r.BaseControl.VisualLabel,null,e.title),(0,l.createElement)(c.Fragment,null,(0,l.createElement)(r.Flex,{gap:6,align:"center",justify:"flex-start"},(0,l.createElement)(r.FlexItem,null,(0,l.createElement)(r.Button,{variant:"secondary",onClick:()=>i(!0)},"Select")),(0,l.createElement)(r.FlexItem,null,(0,l.createElement)(r.ExternalLink,{href:t[e.key].url},t[e.key].title))),s&&(0,l.createElement)(r.Modal,{focusOnMount:!0,shouldCloseOnEsc:!0,shouldCloseOnClickOutside:!0,title:(0,n.__)("Insert/edit link"),onRequestClose:()=>i(!1)},(0,l.createElement)(r.TextControl,{type:"text",label:(0,n.__)("Link text"),value:t[e.key].text||t[e.key].title,onChange:l=>o({[e.key]:{...t[e.key],text:l}})}),(0,l.createElement)(a.__experimentalLinkControl,{value:t[e.key],onChange:t=>o({[e.key]:t})}))));case"file":const u=(e.multiple?t[e.key]:[t[e.key]]).filter((e=>!!e));return(0,l.createElement)(r.BaseControl,null,(0,l.createElement)(r.BaseControl.VisualLabel,null,e.title),(0,l.createElement)(a.MediaUpload,{label:e.title,multiple:e.multiple,value:e.multiple?t[e.key].map((({id:e})=>e)):t[e.key].id,onSelect:t=>{const l=Array.isArray(t)?t.map((({id:e,url:t,title:l,type:n,icon:a})=>({id:e,url:t,title:l,type:n,icon:a}))):{id:t.id,url:t.url,title:t.title,type:t.type,icon:t.icon};o({[e.key]:l})},render:({open:t})=>(0,l.createElement)(r.Flex,{gap:4,wrap:!0},(0,l.createElement)(r.FlexItem,{isBlock:!0},(0,l.createElement)(r.Button,{variant:"secondary",onClick:t},(0,n.__)("Select"))),u.length>=1&&(0,l.createElement)(r.FlexItem,{isBlock:!1},(0,l.createElement)(r.Button,{variant:"secondary",onClick:()=>{o({[e.key]:e.multiple?[]:""})}},e.multiple&&u.length>1?(0,n.__)("Clear"):(0,n.__)("Remove"))),(0,l.createElement)(r.Flex,{gap:4,direction:"column"},u.map((({url:e,title:t,type:n,icon:a})=>(0,l.createElement)(r.FlexItem,{isBlock:!0},(0,l.createElement)(r.Card,null,(0,l.createElement)(r.CardMedia,null,(0,l.createElement)("img",{className:"themeplate-image",src:"image"===n?e:a})),(0,l.createElement)(r.CardFooter,null,t)))))))}));case"editor":case"type":case"post":case"page":case"user":case"term":return(0,l.createElement)(r.BaseControl,{help:e?.help||""},(0,l.createElement)(r.BaseControl.VisualLabel,null,e.title),(0,l.createElement)(r.Tip,null,(0,l.createElement)("strong",null,"TODO!")," Field"," ",(0,l.createElement)("code",null,e.type)))}};function d(e){const{list:t,attributes:n,setAttributes:a}=e;return(0,l.createElement)(c.Fragment,null,t.map((e=>(0,l.createElement)(r.PanelRow,{key:e.key,className:["themeplate-blocks-field",`field-${e.class}`]},p(e,n,a)))))}function k(e){const t=(0,c.useRef)(),i=(0,c.useRef)(),[p,k]=(0,c.useState)([]),[h,E]=(0,c.useState)(!1),y=(0,a.useBlockProps)({className:"wp-block-themeplate",ref:t}),C=y["data-type"],{attributes:b,setAttributes:g}=e,w=(0,o.useSelect)((t=>t(a.store).getBlock(e.clientId)),[e]),f=m.collection[C],B=f.inner_blocks;return(0,c.useMemo)((()=>{fetch(m.ajax_url,{method:"POST",body:new URLSearchParams({_wpnonce:m._wpnonce,action:"themeplate_blocks_fields",block:C})}).then((e=>e.json())).then((e=>{k(e.data),E(!0)}))}),[C]),(0,c.useEffect)((()=>{const e=new MutationObserver((()=>{if(null!==t.current.querySelector(".block-editor-server-side-render")){if(B){const e=t.current.querySelector("ThemePlateInnerBlocks");null===e||e.childNodes.length||e.replaceWith(i.current)}(0,s.doAction)("tpb-rendered",C,t.current),(0,s.doAction)(`tpb-rendered-${C.replace(/\//,".")}`,t.current)}}));return e.observe(t.current,{childList:!0}),()=>{e.disconnect()}}),[C,B]),(0,l.createElement)(c.Fragment,null,(0,l.createElement)(a.InspectorControls,null,!h&&0===p.length&&(0,l.createElement)(r.Placeholder,null,(0,l.createElement)(r.Spinner,null)),h&&0!==p.length&&(0,l.createElement)(r.PanelBody,{title:(0,n.__)("Settings"),className:"themeplate-blocks-fields"},(0,l.createElement)(d,{list:p,attributes:b,setAttributes:g}))),(0,l.createElement)("div",{...y},(0,l.createElement)(u(),{block:C,attributes:b,className:"block-editor-server-side-render"}),B&&(0,l.createElement)(a.InnerBlocks,{ref:i,allowedBlocks:f.allowed_blocks,template:f.template_blocks,templateLock:f.template_lock,renderAppender:w?.innerBlocks?.length?null:a.InnerBlocks.ButtonBlockAppender})))}function h(){return(0,l.createElement)(a.InnerBlocks.Content,null)}Object.keys(m.collection).forEach((e=>{(0,t.registerBlockType)(e,{edit:k,save:h})}))})();