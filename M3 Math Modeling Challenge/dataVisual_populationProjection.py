# The code utilizes existing data on a population to project future population trends for 50 years, using estimated future fertility rates, age distributions, life expectancy, and immigration rates.
# The projections are visualized with confidence intervals and std. deviation calculated using a Monte Carlo simulation to reflect uncertainty due to societal factors such as fertility adjustments and varying immigration rates.
# This code executes the mathematical model presented in the accompanying pdf.

import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
import matplotlib.pyplot as plt

# Load pre-processed data into Pandas data frames
population_data = pd.read_csv(r'C:\miscellaneous\M3\Q1\al_population.csv')
fertility_data = pd.read_csv(r'C:\miscellaneous\M3\Q1\al_fertility.csv')

# Given age distribution in Albuquerque, New Mexico for 2022
age_distribution = {
    '<5': 5.2, '5 to 9': 5.5, '10 to 14': 6.4, 
    '15 to 19': 6.6, '20 to 24': 6.8, '25 to 29': 7.5, 
    '30 to 34': 7.6, '35 to 39': 7.1, '40 to 44': 6.5, 
    '45 to 49': 5.9, '50 to 54': 5.9, '55 to 59': 6.3, 
    '60 to 64': 6.1, '65 to 69': 5.5, '70 to 74': 4.4, 
    '75 to 79': 2.6, '80 to 84': 1.9, '85>': 2.1
}

# Linear regression to predict fertility rate from existing data
model = LinearRegression()
model.fit(fertility_data['Year'].values.reshape(-1, 1), fertility_data['fertility rate'].values.reshape(-1, 1))
future_years = np.arange(2022, 2073).reshape(-1, 1)
base_predicted_fertility = model.predict(future_years).flatten()

# Function to project future population with adjustable inputs for fertility and immigration
def calculate_population_projections(fertility_adjustment, immigration_rate, exponent, exponential_immigration=False):

    reproductive_ages = ['15 to 19', '20 to 24', '25 to 29', '30 to 34', '35 to 39', '40 to 44', '45 to 49']
    adjusted_fertility = base_predicted_fertility * fertility_adjustment
    percent_reproductive_age = sum([age_distribution[age] for age in reproductive_ages])
    women_reproductive_age = percent_reproductive_age * 0.01 * population_data['Total Population'].iloc[-1] * 0.5
    annualized_tfr = adjusted_fertility / 35
    annual_births = women_reproductive_age * annualized_tfr
    population_projections = [population_data['Total Population'].iloc[-1]]

    initial_life_expectancy = 75.5
    annual_increase_in_life_expectancy = 0.157808219

  # Iterates over future years to calculate annual population changes
    for i in range(1, len(future_years)):
        births = annual_births[i - 1]
        current_life_expectancy = initial_life_expectancy + i * annual_increase_in_life_expectancy # Update the life expectancy for the current year
        deaths = population_projections[-1] / current_life_expectancy
        natural_change = births - deaths
        immigration = immigration_rate * (exponent ** (i - 1)) if exponential_immigration else immigration_rate
        projected_population = population_projections[-1] + natural_change + immigration
        population_projections.append(projected_population)

    return population_projections

# Parameters for the normal distributions (mean and standard deviation)
immigration_constant = 2148.8 # Calculated separately

fertility_rate_mean = 1.0  
fertility_rate_std = 0.05

exponent_mean = 1.0126
exponent_std = 0.01

# Run Monte Carlo simulation to generate confidence intervals, assuming random fluctuation in parameters
    def simulate_population_projections(n_simulations, fertility_rate_mean, fertility_rate_std, exponent_mean, exponent_std):
        projections = []
        for _ in range(n_simulations):
            fertility_adjustment = np.random.normal(fertility_rate_mean, fertility_rate_std)
            exponent = np.random.normal(exponent_mean, exponent_std)
            projection = calculate_population_projections(fertility_adjustment, immigration_constant, exponent, True)
            projections.append(projection)
        return np.array(projections)
      
    n_simulations = 1000
    projections = simulate_population_projections(n_simulations, fertility_rate_mean, fertility_rate_std, exponent_mean, exponent_std)

    lower_bound_90 = np.percentile(projections, 2, axis=0)
    upper_bound_90 = np.percentile(projections, 98, axis=0)
    
    lower_bound_80 = np.percentile(projections, 10, axis=0)
    upper_bound_80 = np.percentile(projections, 90, axis=0)



# "Most probable" projection based on specific parameters 
most_probable_projection = calculate_population_projections(1.0, immigration_constant, 1.0126, True)


# Plotting graph with confidence interval
plt.figure(figsize=(10, 6))
plt.plot(population_data['Year'], population_data['Total Population'], 'k-', label='Observed Population')
plt.plot(future_years.flatten(), most_probable_projection, label='Projected Population (most probable)')
plt.fill_between(future_years.flatten(), lower_bound_90, upper_bound_90, color='gray', alpha=0.3, label='96% Confidence Interval')

# Find and plot the standard deviation, as a shaded area around the most probable projection
std_devs = np.std(projections, axis=0)
plt.fill_between(future_years.flatten(), 
                 most_probable_projection - std_devs, 
                 most_probable_projection + std_devs, 
                 color='orange', alpha=0.5, label='Standard Deviation')


# Adjust parameters to plot curves with different starting conditions; e.g: 100% fertility with constant immigration
projections_constant_immigration = calculate_population_projections(1.0, immigration_constant, 1.0126, False)
plt.plot(future_years.flatten(), projections_constant_immigration, label='Projected Population (100% fertility, constant immigration)')

# projections_exponential_immigration = calculate_population_projections(1.1, immigration_constant, 1.0326, True)
# plt.plot(future_years.flatten(), projections_exponential_immigration, label='Projected Population (90% fertility, exponential immigration)')

plt.title('Population Projection for Albuquerque, New Mexico with Immigration')
plt.xlabel('Year')
plt.ylabel('Population')
plt.legend(loc='lower left')
plt.grid(True)
plt.show()


