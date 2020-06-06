# FixGrouped

No License figured out yet. Use to get an idea and do better :)

A messy, out of its depth set of plugins fixing Magento2 for Grouped Products, Alpine_FixGrouped

I am out of my depth, all I have done is look around the M2 code, trace things around and try to understand how things are done. 

For example I do not understand how to make sure any of this uses caching properly - and the summary review bit is certainly inefficient.

But perhaps it helps others with the missing pieces on Grouped products

1. Plugin to extend Product Collection with a selection of attributes
Change for your own attributes

2. Plugins for Review / Ratings collection so simple product reviews show on Grouped entry
Much of that is adding subqueries, or in the case of summary a monster union join thing that where there surely must be a better way to query 
(or it should be done at the steps that populate the summary tables but I'm not messing with that)
Not sure I caught all of them, esp around rating.

3. Plugin overriding Product getUrl to return the parent if there is a grouped product parent. 
This is to allow making simple products visible to use third party extensions but not have them go 

4. (not done yet) Replicate a simple version of product page add to cart on product listings
So products can be added to cart from listings, upsell lists etc.
I'd done it on M1 but with knockout.js it's probably a bit more complicated on M2 so not tackled yet. 
