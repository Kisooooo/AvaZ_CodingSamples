# This file processes the master data into a hiearchical .json structure, grouping revenues from transactions by company and country. 
# The output file is used in the circle packing diagram.

import json

# Read master data file containing node data
with open("MC3.json", encoding="utf-8") as file:
    graph_data = json.load(file)

# Initialize hiearchy structure
data = {
    "name": "Root",
    "children": []
}

# Create and populate a country names dictionary, extracting from each node
country_dict = {}

for node in graph_data["nodes"]:
    country_id = node["country"][0]
    company_id = node["id"][0]
    revenue = node.get("revenue_omu", [0])[0]

    # If country exists in the dictionary, append the company name as a child; else create new country entry and append company as child
    if country_id in country_dict:
        country_dict[country_id]["children"].append({
            "name": company_id,
            "value": revenue
        })
    else:
        country_dict[country_id] = {
            "name": country_id,
            "children": [{
                "name": company_id,
                "value": revenue
            }]
        }

data["children"] = list(country_dict.values())

print(data)

with open("output_data.json", "w") as file:
    json.dump(data, file)






