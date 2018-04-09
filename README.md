# SmartSet
PHP Class that allows advanced operations on arrays of objects

For the following examples, we'll assume a set of objects that look something like table rows from a database:
```php
$objects = [
  (object)[
    'id' => 0,
    'name' => 'Widget',
    'colour' => 'red',
    'quantity' => 5
  ],
  (object)[
    'id' => 1,
    'name' => 'Gadget',
    'colour' => 'red',
    'quantity' => 2
  ],
  (object)[
    'id' => 2,
    'name' => 'Spudger',
    'colour' => 'green',
    'quantity' => 10
  ],
  (object)[
    'id' => 3,
    'name' => 'Gizmo',
    'colour' => 'green',
    'quantity' => 4
  ]
];
```

# Construction
```php
$smartSet = new \D2K\SmartSet($objects);
```

# Get a single object by property
```php
$firstObject = $smartSet('id', 0);
// $firstObject == (object)['id' => 0, ...]
```

# Get a set of objects by property
```php
$redObjects = $smartSet('colour', 'red');
// $redObjects == \D2K\SmartSet([
//    (object)['id' => 0, ...],
//    (object)['id' => 1, ...] ])
```

# Use a unique property as the index
```php
$byID = $smartSet->index('id');
// $byID == [
//    0 => (object)['id' => 0, ...],
//    1 => (object)['id' => 1, ...],
//    ...]
```

# Get groups indexed by property
```php
$byColour = $smartSet->index('colour', true);
// $byColour == [
//    'red' => [
//        (object)['id' => 0, ...],
//        (object)['id' => 1, ...] ],
//    'green' => [
//        (object)['id' => 2, ...],
//        (object)['id' => 3, ...] ]
//    ]
```

# Get a property across the set
```php
$names = $smartSet->property('name');
// $names == ['Widget', 'Gadget', 'Spudger', 'Gizmo']
```

# Sort items
```php
function byQuantity($a, $b)
{
  return $a->quantity <=> $b->quantity;
}
$smartSet->sort('byQuantity');
```

# Transform all items
```php
function addOne($index, $item)
{
  $item->quantity++;
}
$smartSet->map('addOne');
```

# Get the result of function call on each item
```php
function describe($index, $item)
{
  return $item->colour . ' ' . $item->name;
}
$descriptions = $smartSet->each('describe');
// $descriptions == ['red Widget', 'red Gadget', 'green Spudger', 'green Gizmo']
```

# Get the result of a method call on each item
Let's imagine each object has the following public method:
```php
function inStock($minimumStock = 0)
{
  return $this->quantity > $minimumStock;
}
```
Then we can call this method across the set, with a minimumStock of 3 like so:
```php
$hasStock = $smartSet->callMethod('inStock', [3]);
// $hasStock == [ true, false, true, true ]
```

SmartSet implements Iterator and Countable, so you can use it like a normal array as well:
```php
$redGadget = $smartSet[1];
```
