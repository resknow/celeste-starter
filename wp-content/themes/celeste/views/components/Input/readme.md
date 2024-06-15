## Properties

| Prop | Description |
| --- | --- |
| `name` | Input name |
| `id` | Unique ID, if not set, name will be used as the ID. |
| `label` | Input label |
| `type` | Input type, any valid HTML text input type |
| `showLabel` | Whether or not to show the label, defaults to `true`. If set to `false`, the label will still be present, but hidden from sighted users. |

### Note

Attributes passed to this component will be rendered on the `<input>` and not on the root element. This lets you listen for input events as usual.