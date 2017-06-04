# Recipes API

<ul>
<li>To create this API I used Slim 3.0 micro framework as it is lightweight (very small codebase), fast, has minimal overhead
and contains bare necessities which makes it perfect to build APIs. Slim essentially receives and process a HTTP request
and returns a HTTP response. Additionally, it provides other additional helpful tools (redirects, helper functions, error handling, etc.).</li>

<li>To cater for both mobile and web application, an indetifier for the source of the request could be used to decide on what data to send back.
Another way to optimise for mobile apps, so instead of full api response let the client specify which fields should be returned. For example,
/recipes could become /recipes?fields=title,season,recipe_cuisine.</li>
</ul>

<table>
    <thead>
        <tr>
            <th>Method</th>
            <th>Type</th>
            <th>Params</th>
            <th>Desc</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>/recipes</td>
            <td>GET</td>
            <td>
                recipe_cuisine (optional) - Fetch all recipes for a specific cuisine<br>
                <strong>Pagination</strong><br>
                current_page (optional) - Current page number<br>
                per_page (optional) - Number of recipes per to show per page
            </td>
            <td>returns all / filtered recipes</td>
        </tr>
        <tr>
            <td>/recipes/{id}</td>
            <td>GET</td>
            <td>
                none
            </td>
            <td>returns a recipe by id</td>
        </tr>
        <tr>
            <td>/recipes</td>
            <td>POST</td>
            <td>
                <strong>All params are mandatory</strong><br>
                box_type - box type<br>
                title - recipe title<br>
                marketing_description - marketing description<br>
                calories_kcal - amount of calories in kcal<br>
                protein_grams - grams of protein<br>
                fat_grams - grams of fat<br>
                carbs_grams - grams of carbs<br>
                recipe_diet_type_id - recipe diet type id<br>
                season - recipe season<br>
                protein_source - source of protein<br>
                preparation_time_minutes - preparation time in minutes<br>
                shelf_life_days - shelf life days<br>
                equipment_needed - equipment needed<br>
                origin_country - country of origin<br>
                recipe_cuisine - recipe cuisine<br>
                gousto_reference - gousto reference<br>
            </td>
            <td>creates a new recipe</td>
        </tr>
        <tr>
            <td>/recipes/{id}</td>
            <td>PUT</td>
            <td>
                <strong>All params are optional</strong><br>
                box_type - box type<br>
                title - recipe title<br>
                marketing_description - marketing description<br>
                calories_kcal - amount of calories in kcal<br>
                protein_grams - grams of protein<br>
                fat_grams - grams of fat<br>
                carbs_grams - grams of carbs<br>
                recipe_diet_type_id - recipe diet type id<br>
                season - recipe season<br>
                protein_source - source of protein<br>
                preparation_time_minutes - preparation time in minutes<br>
                shelf_life_days - shelf life days<br>
                equipment_needed - equipment needed<br>
                origin_country - country of origin<br>
                recipe_cuisine - recipe cuisine<br>
                gousto_reference - gousto reference<br>
            </td>
            <td>updates an existing recipe</td>
        </tr>
        <tr>
            <td>/recipes/{id}/rating</td>
            <td>PUT</td>
            <td>
                rating - rate a recipe by giving a score betwwen 1 and 5<br>
            </td>
            <td>rates a recipe</td>
        </tr>
    </tbody>
</table>
